<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdmissionPayment;
use App\Models\AdmissionRenewal;
use App\Models\StaffPayment;
use App\Models\TeacherPayment;
use App\Services\ReceiptPdfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class TransactionPaymentController extends Controller
{
    /**
     * Student payments tab: paid + pending renewals.
     */
    public function student(Request $request): JsonResponse
    {
        $studentId = $request->user()->id;

        return response()->json([
            'success' => true,
            'data' => [
                'paid'    => $this->getAdmissionPayments($studentId),
                'pending' => $this->getPendingRenewals($studentId),
            ],
        ]);
    }

    /**
     * Download/share link for a PAID admission receipt (student's own).
     */
    public function studentReceipt(Request $request): JsonResponse
    {
        $request->validate(['payment_id' => ['required', 'integer']]);

        $payment = AdmissionPayment::with(['student:id,name', 'course:id,name'])
            ->where('student_id', $request->user()->id)
            ->find($request->integer('payment_id'));

        if (! $payment) {
            return $this->notFound('Receipt not found.');
        }

        $path = 'receipts/' . ReceiptPdfService::buildFilename('receipt_admission', $payment->id, $payment->transaction_no);

        if (! Storage::disk('public')->exists($path)) {
            ReceiptPdfService::generateAdmissionReceipt($payment, $path);
        }

        return $this->fileResponse($path, sprintf(
            "%s Payment Receipt\nAmount Paid: ₹%s | %s",
            config('app.name'),
            number_format((float) $payment->amount, 2),
            optional($payment->course)->name
        ));
    }

    /**
     * Download link for a PENDING fee invoice (student's own renewal).
     */
    public function studentPendingInvoice(Request $request): JsonResponse
    {
        $request->validate(['renewal_id' => ['required', 'integer']]);

        $renewal = AdmissionRenewal::with(['student:id,name', 'course:id,name'])
            ->where('student_id', $request->user()->id)
            ->where('status', 'pending')
            ->find($request->integer('renewal_id'));

        if (! $renewal) {
            return $this->notFound('Pending invoice not found.');
        }

        $path = 'invoices/' . ReceiptPdfService::buildFilename('invoice_renewal', $renewal->id);

        if (! Storage::disk('public')->exists($path)) {
            ReceiptPdfService::generateAdmissionInvoice($renewal, $path);
        }

        return $this->fileResponse($path, sprintf(
            "%s Fee Due\nAmount: ₹%s | %s\nDue: %s",
            config('app.name'),
            number_format((float) $renewal->final_amount, 2),
            optional($renewal->course)->name,
            $renewal->current_expiry_date
        ));
    }

    /**
     * Teacher payments tab: pending release + released.
     */
    public function teacher(Request $request): JsonResponse
    {
        $teacherId = $request->user()->id;

        return response()->json([
            'success' => true,
            'data' => [
                'pending_release' => $this->getTeacherPayments('pending', $teacherId),
                'released'        => $this->getTeacherPayments('released', $teacherId),
            ],
        ]);
    }

    /**
     * Download/share link for a RELEASED teacher payment receipt (own).
     */
    public function teacherReceipt(Request $request): JsonResponse
    {
        $request->validate(['payment_id' => ['required', 'integer']]);

        $payment = TeacherPayment::with(['teacher:id,name', 'releasedBy:id,name', 'items.course:id,name'])
            ->where('teacher_id', $request->user()->id)
            ->where('status', 'released')
            ->find($request->integer('payment_id'));

        if (! $payment) {
            return $this->notFound('Receipt not found.');
        }

        $path = 'receipts/' . ReceiptPdfService::buildFilename('receipt_teacher', $payment->id, $payment->transaction_no);

        if (! Storage::disk('public')->exists($path)) {
            ReceiptPdfService::generateTeacherReceipt($payment, $path);
        }

        return $this->fileResponse($path, sprintf(
            "%s Payment Receipt\nAmount Paid: ₹%s | %s to %s",
            config('app.name'),
            number_format((float) $payment->amount, 2),
            $payment->period_start,
            $payment->period_end
        ));
    }

    /**
     * Download link for a PENDING teacher payment invoice (own).
     */
    public function teacherPendingInvoice(Request $request): JsonResponse
    {
        $request->validate(['payment_id' => ['required', 'integer']]);

        $payment = TeacherPayment::with(['teacher:id,name', 'items.course:id,name'])
            ->where('teacher_id', $request->user()->id)
            ->where('status', 'pending')
            ->find($request->integer('payment_id'));

        if (! $payment) {
            return $this->notFound('Pending invoice not found.');
        }

        $path = 'invoices/' . ReceiptPdfService::buildFilename('invoice_teacher', $payment->id);

        if (! Storage::disk('public')->exists($path)) {
            ReceiptPdfService::generateTeacherInvoice($payment, $path);
        }

        return $this->fileResponse($path, sprintf(
            "%s Payment Due\nAmount: ₹%s | %s to %s",
            config('app.name'),
            number_format((float) $payment->amount, 2),
            $payment->period_start,
            $payment->period_end
        ));
    }

    /**
     * Staff payments tab: pending release + released.
     */
    public function staff(Request $request): JsonResponse
    {
        $staffId = $request->user()->id;

        return response()->json([
            'success' => true,
            'data' => [
                'pending_release' => $this->getStaffPayments('pending', $staffId),
                'released'        => $this->getStaffPayments('released', $staffId),
            ],
        ]);
    }

    /**
     * Download/share link for a RELEASED staff salary receipt (own).
     */
    public function staffReceipt(Request $request): JsonResponse
    {
        $request->validate(['payment_id' => ['required', 'integer']]);

        $payment = StaffPayment::with(['staff:id,name', 'releasedBy:id,name'])
            ->where('staff_id', $request->user()->id)
            ->where('status', 'released')
            ->find($request->integer('payment_id'));

        if (! $payment) {
            return $this->notFound('Receipt not found.');
        }

        $path = 'receipts/' . ReceiptPdfService::buildFilename('receipt_staff', $payment->id, $payment->transaction_no);

        if (! Storage::disk('public')->exists($path)) {
            ReceiptPdfService::generateStaffReceipt($payment, $path);
        }

        return $this->fileResponse($path, sprintf(
            "%s Salary Receipt\nAmount Paid: ₹%s | %s",
            config('app.name'),
            number_format((float) $payment->final_amount, 2),
            $payment->salary_month
        ));
    }

    /**
     * Download link for a PENDING staff salary invoice (own).
     */
    public function staffPendingInvoice(Request $request): JsonResponse
    {
        $request->validate(['payment_id' => ['required', 'integer']]);

        $payment = StaffPayment::with(['staff:id,name'])
            ->where('staff_id', $request->user()->id)
            ->where('status', 'pending')
            ->find($request->integer('payment_id'));

        if (! $payment) {
            return $this->notFound('Pending invoice not found.');
        }

        $path = 'invoices/' . ReceiptPdfService::buildFilename('invoice_staff', $payment->id);

        if (! Storage::disk('public')->exists($path)) {
            ReceiptPdfService::generateStaffInvoice($payment, $path);
        }

        return $this->fileResponse($path, sprintf(
            "%s Salary Due\nAmount: ₹%s | %s",
            config('app.name'),
            number_format((float) $payment->final_amount, 2),
            $payment->salary_month
        ));
    }

    /**
     * Admin overview tab: combines everything above.
     */
    public function admin(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'pending_collection' => $this->getPendingRenewals(),

                'pending_release' => [
                    'teachers' => $this->getTeacherPayments('pending'),
                    'staff'    => $this->getStaffPayments('pending'),
                ],

                'released' => [
                    'teachers' => $this->getTeacherPayments('released'),
                    'staff'    => $this->getStaffPayments('released'),
                ],

                'collected' => $this->getAdmissionPayments(),
            ],
        ]);
    }

    /**
     * Admin: download/share link for ANY receipt or pending invoice,
     * across student/teacher/staff — by type + id, no ownership scoping.
     */
    public function adminReceipt(Request $request): JsonResponse
    {
        $request->validate([
            'type' => ['required', 'in:admission_paid,admission_pending,teacher_released,teacher_pending,staff_released,staff_pending'],
            'id'   => ['required', 'integer'],
        ]);

        [$path, $message] = match ($request->string('type')->toString()) {
            'admission_paid' => $this->buildAdmissionReceipt($request->integer('id')),
            'admission_pending' => $this->buildAdmissionInvoice($request->integer('id')),
            'teacher_released' => $this->buildTeacherReceipt($request->integer('id')),
            'teacher_pending' => $this->buildTeacherInvoice($request->integer('id')),
            'staff_released' => $this->buildStaffReceipt($request->integer('id')),
            'staff_pending' => $this->buildStaffInvoice($request->integer('id')),
        };

        if (! $path) {
            return $this->notFound('Record not found.');
        }

        return $this->fileResponse($path, $message);
    }

    private function buildAdmissionReceipt(int $id): array
    {
        $payment = AdmissionPayment::with(['student:id,name', 'course:id,name'])->find($id);
        if (! $payment) return [null, null];

        $path = 'receipts/' . ReceiptPdfService::buildFilename('receipt_admission', $payment->id, $payment->transaction_no);
        if (! Storage::disk('public')->exists($path)) {
            ReceiptPdfService::generateAdmissionReceipt($payment, $path);
        }

        return [$path, "Amount Paid: ₹" . number_format((float) $payment->amount, 2) . ' | ' . optional($payment->course)->name];
    }

    private function buildAdmissionInvoice(int $id): array
    {
        $renewal = AdmissionRenewal::with(['student:id,name', 'course:id,name'])->find($id);
        if (! $renewal) return [null, null];

        $path = 'invoices/' . ReceiptPdfService::buildFilename('invoice_renewal', $renewal->id);
        if (! Storage::disk('public')->exists($path)) {
            ReceiptPdfService::generateAdmissionInvoice($renewal, $path);
        }

        return [$path, "Amount Due: ₹" . number_format((float) $renewal->final_amount, 2) . ' | ' . optional($renewal->course)->name];
    }

    private function buildTeacherReceipt(int $id): array
    {
        $payment = TeacherPayment::with(['teacher:id,name', 'items.course:id,name'])->find($id);
        if (! $payment) return [null, null];

        $path = 'receipts/' . ReceiptPdfService::buildFilename('receipt_teacher', $payment->id, $payment->transaction_no);
        if (! Storage::disk('public')->exists($path)) {
            ReceiptPdfService::generateTeacherReceipt($payment, $path);
        }

        return [$path, "Amount Paid: ₹" . number_format((float) $payment->amount, 2)];
    }

    private function buildTeacherInvoice(int $id): array
    {
        $payment = TeacherPayment::with(['teacher:id,name', 'items.course:id,name'])->find($id);
        if (! $payment) return [null, null];

        $path = 'invoices/' . ReceiptPdfService::buildFilename('invoice_teacher', $payment->id);
        if (! Storage::disk('public')->exists($path)) {
            ReceiptPdfService::generateTeacherInvoice($payment, $path);
        }

        return [$path, "Amount Due: ₹" . number_format((float) $payment->amount, 2)];
    }

    private function buildStaffReceipt(int $id): array
    {
        $payment = StaffPayment::with(['staff:id,name'])->find($id);
        if (! $payment) return [null, null];

        $path = 'receipts/' . ReceiptPdfService::buildFilename('receipt_staff', $payment->id, $payment->transaction_no);
        if (! Storage::disk('public')->exists($path)) {
            ReceiptPdfService::generateStaffReceipt($payment, $path);
        }

        return [$path, "Amount Paid: ₹" . number_format((float) $payment->final_amount, 2)];
    }

    private function buildStaffInvoice(int $id): array
    {
        $payment = StaffPayment::with(['staff:id,name'])->find($id);
        if (! $payment) return [null, null];

        $path = 'invoices/' . ReceiptPdfService::buildFilename('invoice_staff', $payment->id);
        if (! Storage::disk('public')->exists($path)) {
            ReceiptPdfService::generateStaffInvoice($payment, $path);
        }

        return [$path, "Amount Due: ₹" . number_format((float) $payment->final_amount, 2)];
    }

    /**
     * Build the standard receipt/invoice JSON response (url + whatsapp link).
     */
    private function fileResponse(string $path, string $messageBody): JsonResponse
    {
        $url = Storage::disk('public')->url($path);

        $message = sprintf("%s\nDownload: %s", $messageBody, $url);

        return response()->json([
            'success' => true,
            'data' => [
                'receipt_url'    => $url,
                'filename'       => basename($path),
                'whatsapp_url'   => 'https://wa.me/?text=' . rawurlencode($message),
                'preview_base64' => null,
            ],
        ]);
    }

    private function notFound(string $message): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 404);
    }

    /**
     * Admission payments already paid by students.
     */
    private function getAdmissionPayments(?int $studentId = null): Collection
    {
        return AdmissionPayment::with([
            'student:id,name',
            'course:id,name',
            'admission:id',
        ])
            ->when($studentId, fn ($query) => $query->where('student_id', $studentId))
            ->orderByDesc('paid_at')
            ->get()
            ->map(function ($payment) {
                return [
                    'id'             => $payment->id,
                    'admission_id'   => $payment->admission_id,
                    'student_id'     => $payment->student_id,
                    'student_name'   => optional($payment->student)->name,
                    'course_id'      => $payment->course_id,
                    'course_name'    => optional($payment->course)->name,
                    'amount'         => (float) $payment->amount,
                    'payment_method' => $payment->payment_method,
                    'transaction_no' => $payment->transaction_no,
                    'remarks'        => $payment->remarks,
                    'paid_at'        => optional($payment->paid_at)->toDateTimeString(),
                    'received_by'    => $payment->receiver?->name ?? "",
                    'created_at'     => optional($payment->created_at)->toDateTimeString(),
                ];
            })
            ->values();
    }

    /**
     * Renewals awaiting payment (pending collection).
     */
    private function getPendingRenewals(?int $studentId = null): Collection
    {
        return AdmissionRenewal::with([
            'student:id,name',
            'course:id,name',
            'admission:id',
        ])
            ->where('status', 'pending')
            ->when($studentId, fn ($query) => $query->where('student_id', $studentId))
            ->latest()
            ->get()
            ->map(function ($renewal) {
                return [
                    'id'                  => $renewal->id,
                    'admission_id'        => $renewal->admission_id,
                    'student_id'          => $renewal->student_id,
                    'student_name'        => optional($renewal->student)->name,
                    'course_id'           => $renewal->course_id,
                    'course_name'         => optional($renewal->course)->name,
                    'current_expiry_date' => $renewal->current_expiry_date,
                    'renewal_from'        => $renewal->renewal_from,
                    'renewal_to'          => $renewal->renewal_to,
                    'amount'              => (float) $renewal->amount,
                    'discount_amount'     => (float) $renewal->discount_amount,
                    'final_amount'        => (float) $renewal->final_amount,
                    'paid_at'             => optional($renewal->paid_at)->toDateTimeString(),
                    'status'              => $renewal->status,
                    'remarks'             => $renewal->remarks,
                    'created_at'          => optional($renewal->created_at)->toDateTimeString(),
                ];
            })
            ->values();
    }

    /**
     * Teacher payment batches, filtered by status ('pending' | 'released').
     */
    private function getTeacherPayments(string $status, ?int $teacherId = null): Collection
    {
        $query = TeacherPayment::with([
            'teacher:id,name',
            'createdBy:id,name',
            'releasedBy:id,name',
            'items.course:id,name',
        ])
            ->where('status', $status)
            ->when($teacherId, fn ($q) => $q->where('teacher_id', $teacherId));

        $query = $status === 'released'
            ? $query->latest('payment_date')
            : $query->latest();

        return $query->get()->map(function ($payment) {
            return [
                'id'           => $payment->id,
                'teacher_id'   => $payment->teacher_id,
                'teacher_name' => optional($payment->teacher)->name,

                'period_start' => $payment->period_start,
                'period_end'   => $payment->period_end,

                'total_classes' => $payment->total_classes,

                'gross_amount'     => (float) $payment->gross_amount,
                'deduction_amount' => (float) $payment->deduction_amount,
                'deduction_reason' => $payment->deduction_reason,
                'amount'           => (float) $payment->amount,

                'payment_method'    => $payment->payment_method,
                'payment_reference' => $payment->payment_reference,
                'transaction_no'    => $payment->transaction_no,
                'payment_date'      => $payment->payment_date,

                'remarks'  => $payment->remarks,
                'status'   => $payment->status,
                'paid_at'  => $payment->paid_at,

                'released_by_name' => optional($payment->releasedBy)->name,
                'created_by_name'  => optional($payment->createdBy)->name,

                'items' => $payment->items->map(function ($item) {
                    return [
                        'id'               => $item->id,
                        'course_id'        => $item->course_id,
                        'course_name'      => optional($item->course)->name,
                        'month'            => $item->month,
                        'calculation_type' => $item->calculation_type,
                        'student_count'    => $item->student_count,
                        'course_revenue'   => (float) $item->course_revenue,
                        'share_percentage' => (float) $item->share_percentage,
                        'amount'           => (float) $item->amount,
                        'remarks'          => $item->remarks,
                        'status'           => $item->status,
                    ];
                })->values(),
            ];
        })->values();
    }

    /**
     * Staff salary payments, filtered by status ('pending' | 'released').
     */
    private function getStaffPayments(string $status, ?int $staffId = null): Collection
    {
        $query = StaffPayment::with([
            'staff:id,name',
            'createdBy:id,name',
            'releasedBy:id,name',
        ])
            ->where('status', $status)
            ->when($staffId, fn ($q) => $q->where('staff_id', $staffId));

        $query = $status === 'released'
            ? $query->latest('payment_date')
            : $query->latest();

        return $query->get()->map(function ($payment) {
            return [
                'id'         => $payment->id,
                'staff_id'   => $payment->staff_id,
                'staff_name' => optional($payment->staff)->name,

                'salary_month'     => $payment->salary_month,
                'salary_amount'    => (float) $payment->salary_amount,
                'bonus_amount'     => (float) $payment->bonus_amount,
                'deduction_amount' => (float) $payment->deduction_amount,
                'deduction_reason' => $payment->deduction_reason,
                'final_amount'     => (float) $payment->final_amount,

                'payment_method' => $payment->payment_method,
                'transaction_no' => $payment->transaction_no,
                'payment_date'   => $payment->payment_date,

                'remarks' => $payment->remarks,
                'status'  => $payment->status,
                'paid_at' => $payment->paid_at,

                'released_by_name' => optional($payment->releasedBy)->name,
                'created_by_name'  => optional($payment->createdBy)->name,

                'created_at' => $payment->created_at,
            ];
        })->values();
    }
}
