<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdmissionPaymentResource;
use App\Http\Resources\AdmissionRenewalResource;
use App\Http\Resources\Payments\PaymentReceiptResource;
use App\Http\Resources\Payments\TeacherPaymentResource;
use App\Http\Resources\StaffPaymentResource;
use App\Models\AdmissionPayment;
use App\Models\AdmissionRenewal;
use App\Models\TeacherPayment;
use App\Models\TeacherPaymentItem;
use App\Models\StaffPayment;
use App\Services\ReceiptService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AppPaymentController extends Controller
{
    protected ReceiptService $receiptService;

    public function __construct(ReceiptService $receiptService)
    {
        $this->receiptService = $receiptService;
    }

    // ─────────────────────────────────────────────────────────────────
    // STUDENT PAYMENTS
    // ─────────────────────────────────────────────────────────────────

    /**
     * GET /api/payments/student
     * Returns paid + pending admission payments for the authenticated student.
     */
    public function studentPayments(Request $request): JsonResponse
    {
        $studentId = $request->query('student_id') ?? Auth::id();

        $paid = AdmissionPayment::with(['course', 'receivedBy'])
            ->where('student_id', $studentId)
            ->whereNotNull('paid_at')
            ->orderByDesc('paid_at')
            ->get();

        $unpaidRenewals = AdmissionRenewal::with('course')
            ->where('student_id', $studentId)
            ->where('status', 'pending')
            ->orderBy('renewal_to')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'paid'    => AdmissionPaymentResource::collection($paid),
                'pending' => AdmissionRenewalResource::collection($unpaidRenewals),
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────
    // TEACHER PAYMENTS
    // ─────────────────────────────────────────────────────────────────

    /**
     * GET /api/payments/teacher
     * Returns pending (unreleased) and released teacher payments.
     */
    public function teacherPayments(Request $request): JsonResponse
    {
        $teacherId = $request->query('teacher_id') ?? Auth::id();

        $pending = TeacherPayment::with(['items.course', 'createdBy'])
            ->where('teacher_id', $teacherId)
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->get();

        $released = TeacherPayment::with(['items.course', 'releasedBy'])
            ->where('teacher_id', $teacherId)
            ->where('status', 'released')
            ->orderByDesc('paid_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'pending_release' => TeacherPaymentResource::collection($pending),
                'released'        => TeacherPaymentResource::collection($released),
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────
    // STAFF / ADMIN PAYMENTS (4-tab view)
    // ─────────────────────────────────────────────────────────────────

    /**
     * GET /api/payments/admin
     * Returns all 4 admin payment categories.
     */
    public function adminPayments(Request $request): JsonResponse
    {
        // Tab 1 — Pending student collections (admissions not yet paid)
        $pendingCollection = AdmissionRenewal::with(['student', 'course'])
            ->where('status', 'pending')
            ->orderBy('renewal_to')
            ->get();

        // Tab 2 — Pending releases (teacher + staff salaries not yet paid out)
        $pendingTeacherRelease = TeacherPayment::with(['teacher', 'items.course'])
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->get();

        $pendingStaffRelease = StaffPayment::with(['staff', 'releasedBy'])
            ->where('status', 'pending')
            ->orderBy('salary_month')
            ->get();

        // Tab 3 — Released payments (teacher + staff payouts completed)
        $releasedTeacher = TeacherPayment::with(['teacher', 'releasedBy'])
            ->where('status', 'released')
            ->orderByDesc('paid_at')
            ->limit(100)
            ->get();

        $releasedStaff = StaffPayment::with(['staff', 'releasedBy'])
            ->where('status', 'paid')
            ->orderByDesc('paid_at')
            ->limit(100)
            ->get();

        // Tab 4 — Collected payments (admission payments received)
        $collected = AdmissionPayment::with(['student', 'course', 'receivedBy'])
            ->orderByDesc('paid_at')
            ->limit(200)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'pending_collection'  => AdmissionRenewalResource::collection($pendingCollection),
                'pending_release'     => [
                    'teachers' => TeacherPaymentResource::collection($pendingTeacherRelease),
                    'staff'    => StaffPaymentResource::collection($pendingStaffRelease),
                ],
                'released'            => [
                    'teachers' => TeacherPaymentResource::collection($releasedTeacher),
                    'staff'    => StaffPaymentResource::collection($releasedStaff),
                ],
                'collected'           => AdmissionPaymentResource::collection($collected),
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────
    // RECEIPT — generate PDF and return download URL + share data
    // ─────────────────────────────────────────────────────────────────

    /**
     * GET /api/payments/receipt/{type}/{id}
     * type: admission | renewal | teacher | staff
     *
     * Returns:
     *   receipt_url   — direct download link (PDF)
     *   whatsapp_url  — whatsapp://send?text=... with file share note
     *   preview       — base64 PNG thumbnail of page 1
     */
    public function receipt(Request $request, string $type, int $id): JsonResponse
    {
        $result = $this->receiptService->generate($type, $id);

        if (!$result) {
            return response()->json(['success' => false, 'message' => 'Receipt not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => new PaymentReceiptResource($result),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────
    // MARK TEACHER PAYMENT AS RELEASED  (admin action)
    // ─────────────────────────────────────────────────────────────────

    /**
     * POST /api/payments/teacher/{id}/release
     */
    public function releaseTeacherPayment(Request $request, int $id): JsonResponse
    {
        $payment = TeacherPayment::findOrFail($id);

        if ($payment->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Already released'], 422);
        }

        $payment->update([
            'status'           => 'released',
            'released_by'      => Auth::id(),
            'paid_at'          => now(),
            'payment_method'   => $request->payment_method ?? 'bank_transfer',
            'transaction_no'   => $request->transaction_no,
            'payment_date'     => now()->toDateString(),
            'remarks'          => $request->remarks,
        ]);

        return response()->json([
            'success' => true,
            'data'    => new TeacherPaymentResource($payment->fresh(['teacher', 'items.course', 'releasedBy'])),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────
    // MARK STAFF SALARY AS PAID  (admin action)
    // ─────────────────────────────────────────────────────────────────

    /**
     * POST /api/payments/staff/{id}/release
     */
    public function releaseStaffPayment(Request $request, int $id): JsonResponse
    {
        $payment = StaffPayment::findOrFail($id);

        if ($payment->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Already paid'], 422);
        }

        $payment->update([
            'status'         => 'paid',
            'released_by'    => Auth::id(),
            'paid_at'        => now(),
            'payment_method' => $request->payment_method ?? 'bank_transfer',
            'transaction_no' => $request->transaction_no,
            'payment_date'   => now()->toDateString(),
            'remarks'        => $request->remarks,
        ]);

        return response()->json([
            'success' => true,
            'data'    => new StaffPaymentResource($payment->fresh(['staff', 'releasedBy'])),
        ]);
    }
}
