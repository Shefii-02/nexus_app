<?php

namespace App\Http\Controllers\API\Admin;

use App\DTOs\TransactionDTO;
use App\Http\Requests\TransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Controllers\Controller;
use App\Models\AdmissionPayment;
use App\Models\AdmissionRenewal;
use App\Models\StaffPayment;
use App\Models\TeacherPayment;

class TransactionPaymentController extends Controller
{

    public function student(Request $request)
    {
        $paid = AdmissionPayment::with([
            'student:id,name',
            'course:id,name',
            'admission:id'
        ])
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
                    'received_by'    => $payment->received_by,
                    'created_at'     => optional($payment->created_at)->toDateTimeString(),
                ];
            });

        $pending = AdmissionRenewal::with([
            'student:id,name',
            'course:id,name',
            'admission:id'
        ])
            ->where('status', 'pending')
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
            });

        return response()->json([
            'success' => true,
            'data' => [
                'paid' => $paid,
                'pending' => $pending,
            ],
        ]);
    }
    // public function student(Request $request)
    // {

    //     return response()->json([
    //         'success' => true,
    //         'data' => [

    //             // Tab 1 — Paid
    //             'paid' => [
    //                 // [
    //                 //     'id'             => 1001,
    //                 //     'admission_id'   => 10,
    //                 //     'student_id'     => 5,
    //                 //     'student_name'   => 'Amal Joshy',
    //                 //     'course_id'      => 1,
    //                 //     'course_name'    => 'Flutter & Dart – Complete Bootcamp',
    //                 //     'amount'         => 4500.00,
    //                 //     'payment_method' => 'upi',
    //                 //     'transaction_no' => 'UPI2024031501',
    //                 //     'remarks'        => 'Admission fee',
    //                 //     'paid_at'        => '2025-04-23T10:30:00.000000Z',
    //                 //     'received_by'    => 'Admin',
    //                 //     'created_at'     => '2025-04-23T10:30:00.000000Z',
    //                 // ],
    //                 // [
    //                 //     'id'             => 1002,
    //                 //     'admission_id'   => 10,
    //                 //     'student_id'     => 5,
    //                 //     'student_name'   => 'Amal Joshy',
    //                 //     'course_id'      => 3,
    //                 //     'course_name'    => 'UI/UX Design Fundamentals',
    //                 //     'amount'         => 3200.00,
    //                 //     'payment_method' => 'bank_transfer',
    //                 //     'transaction_no' => 'TXN20240210',
    //                 //     'remarks'        => null,
    //                 //     'paid_at'        => '2025-03-24T09:00:00.000000Z',
    //                 //     'received_by'    => 'Admin',
    //                 //     'created_at'     => '2025-03-24T09:00:00.000000Z',
    //                 // ],
    //                 // [
    //                 //     'id'             => 1003,
    //                 //     'admission_id'   => 9,
    //                 //     'student_id'     => 5,
    //                 //     'student_name'   => 'Amal Joshy',
    //                 //     'course_id'      => 2,
    //                 //     'course_name'    => 'Laravel REST API Masterclass',
    //                 //     'amount'         => 3800.00,
    //                 //     'payment_method' => 'cash',
    //                 //     'transaction_no' => null,
    //                 //     'remarks'        => 'Paid in office',
    //                 //     'paid_at'        => '2025-02-07T11:00:00.000000Z',
    //                 //     'received_by'    => 'Reception',
    //                 //     'created_at'     => '2025-02-07T11:00:00.000000Z',
    //                 // ],
    //             ],

    //             // Tab 2 — Pending (renewals due)
    //             'pending' => [
    //                 // [
    //                 //     'id'                  => 501,
    //                 //     'admission_id'        => 10,
    //                 //     'student_id'          => 5,
    //                 //     'student_name'        => 'Amal Joshy',
    //                 //     'course_id'           => 1,
    //                 //     'course_name'         => 'Flutter & Dart – Complete Bootcamp',
    //                 //     'current_expiry_date' => '2025-08-10',
    //                 //     'renewal_from'        => '2025-08-11',
    //                 //     'renewal_to'          => '2025-10-10',
    //                 //     'amount'              => 4500.00,
    //                 //     'discount_amount'     => 450.00,
    //                 //     'final_amount'        => 4050.00,
    //                 //     'paid_at'             => null,
    //                 //     'status'              => 'pending',  // 'pending' | 'paid' | 'expired'
    //                 //     'remarks'             => '10% early renewal discount',
    //                 //     'created_at'          => '2025-06-01T00:00:00.000000Z',
    //                 // ],
    //                 // [
    //                 //     'id'                  => 502,
    //                 //     'admission_id'        => 11,
    //                 //     'student_id'          => 5,
    //                 //     'student_name'        => 'Amal Joshy',
    //                 //     'course_id'           => 3,
    //                 //     'course_name'         => 'UI/UX Design Fundamentals',
    //                 //     'current_expiry_date' => '2025-06-02',
    //                 //     'renewal_from'        => '2025-06-02',
    //                 //     'renewal_to'          => '2025-08-02',
    //                 //     'amount'              => 3200.00,
    //                 //     'discount_amount'     => 0.00,
    //                 //     'final_amount'        => 3200.00,
    //                 //     'paid_at'             => null,
    //                 //     'status'              => 'pending',
    //                 //     'remarks'             => null,
    //                 //     'created_at'          => '2025-05-20T00:00:00.000000Z',
    //                 // ],
    //             ],
    //         ],
    //     ]);
    // }

    public function teacher(Request $request)
    {
        $pendingRelease = TeacherPayment::with([
            'teacher:id,name',
            'createdBy:id,name',
            'releasedBy:id,name',
            'items.course:id,name'
        ])
            ->where('status', 'pending')
            ->latest()
            ->get()
            ->map(function ($payment) {

                return [
                    'id'                => $payment->id,
                    'teacher_id'        => $payment->teacher_id,
                    'teacher_name'      => optional($payment->teacher)->name,

                    'period_start'      => $payment->period_start,
                    'period_end'        => $payment->period_end,

                    'total_classes'     => $payment->total_classes,

                    'gross_amount'      => (float) $payment->gross_amount,
                    'deduction_amount'  => (float) $payment->deduction_amount,
                    'deduction_reason'  => $payment->deduction_reason,
                    'amount'            => (float) $payment->amount,

                    'payment_method'    => $payment->payment_method,
                    'payment_reference' => $payment->payment_reference,
                    'transaction_no'    => $payment->transaction_no,
                    'payment_date'      => $payment->payment_date,

                    'remarks'           => $payment->remarks,
                    'status'            => $payment->status,
                    'paid_at'           => $payment->paid_at,

                    'released_by_name'  => optional($payment->releasedBy)->name,
                    'created_by_name'   => optional($payment->createdBy)->name,

                    'items' => $payment->items->map(function ($item) {
                        return [
                            'id'                => $item->id,
                            'course_id'         => $item->course_id,
                            'course_name'       => optional($item->course)->name,
                            'month'             => $item->month,
                            'calculation_type'  => $item->calculation_type,
                            'student_count'     => $item->student_count,
                            'course_revenue'    => (float) $item->course_revenue,
                            'share_percentage'  => (float) $item->share_percentage,
                            'amount'            => (float) $item->amount,
                            'remarks'           => $item->remarks,
                            'status'            => $item->status,
                        ];
                    })->values(),
                ];
            });

        $released = TeacherPayment::with([
            'teacher:id,name',
            'createdBy:id,name',
            'releasedBy:id,name',
            'items.course:id,name'
        ])
            ->where('status', 'released')
            ->latest('payment_date')
            ->get()
            ->map(function ($payment) {

                return [
                    'id'                => $payment->id,
                    'teacher_id'        => $payment->teacher_id,
                    'teacher_name'      => optional($payment->teacher)->name,

                    'period_start'      => $payment->period_start,
                    'period_end'        => $payment->period_end,

                    'total_classes'     => $payment->total_classes,

                    'gross_amount'      => (float) $payment->gross_amount,
                    'deduction_amount'  => (float) $payment->deduction_amount,
                    'deduction_reason'  => $payment->deduction_reason,
                    'amount'            => (float) $payment->amount,

                    'payment_method'    => $payment->payment_method,
                    'payment_reference' => $payment->payment_reference,
                    'transaction_no'    => $payment->transaction_no,
                    'payment_date'      => $payment->payment_date,

                    'remarks'           => $payment->remarks,
                    'status'            => $payment->status,
                    'paid_at'           => $payment->paid_at,

                    'released_by_name'  => optional($payment->releasedBy)->name,
                    'created_by_name'   => optional($payment->createdBy)->name,

                    'items' => $payment->items->map(function ($item) {
                        return [
                            'id'                => $item->id,
                            'course_id'         => $item->course_id,
                            'course_name'       => optional($item->course)->name,
                            'month'             => $item->month,
                            'calculation_type'  => $item->calculation_type,
                            'student_count'     => $item->student_count,
                            'course_revenue'    => (float) $item->course_revenue,
                            'share_percentage'  => (float) $item->share_percentage,
                            'amount'            => (float) $item->amount,
                            'remarks'           => $item->remarks,
                            'status'            => $item->status,
                        ];
                    })->values(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'pending_release' => $pendingRelease,
                'released' => $released,
            ],
        ]);
    }

    // public function teacher(Request $request)
    // {
    //     return response()->json([
    //         'success' => true,
    //         'data' => [

    //             // Tab 1 — Pending release
    //             'pending_release' => [
    //                 // [
    //                 //     'id'               => 301,
    //                 //     'teacher_id'       => 7,
    //                 //     'teacher_name'     => 'Arjun Menon',
    //                 //     'period_start'     => '2025-05-01',
    //                 //     'period_end'       => '2025-05-31',
    //                 //     'total_classes'    => 8,
    //                 //     'gross_amount'     => 12000.00,
    //                 //     'deduction_amount' => 0.00,
    //                 //     'deduction_reason' => null,
    //                 //     'amount'           => 12000.00,
    //                 //     'payment_method'   => null,
    //                 //     'payment_reference' => null,
    //                 //     'transaction_no'   => null,
    //                 //     'payment_date'     => null,
    //                 //     'remarks'          => null,
    //                 //     'status'           => 'pending',
    //                 //     'paid_at'          => null,
    //                 //     'released_by_name' => null,
    //                 //     'created_by_name'  => 'Admin',
    //                 //     'items' => [
    //                 //         [
    //                 //             'id'               => 3011,
    //                 //             'course_id'        => 1,
    //                 //             'course_name'      => 'Flutter & Dart – Complete Bootcamp',
    //                 //             'month'            => 'May 2025',
    //                 //             'calculation_type' => 'percentage', // 'percentage' | 'per_class' | 'fixed'
    //                 //             'student_count'    => 14,
    //                 //             'course_revenue'   => 63000.00,
    //                 //             'share_percentage' => 15.0,
    //                 //             'amount'           => 9450.00,
    //                 //             'remarks'          => null,
    //                 //             'status'           => 'pending',
    //                 //         ],
    //                 //         [
    //                 //             'id'               => 3012,
    //                 //             'course_id'        => 4,
    //                 //             'course_name'      => 'Python for Data Science',
    //                 //             'month'            => 'May 2025',
    //                 //             'calculation_type' => 'per_class',
    //                 //             'student_count'    => 8,
    //                 //             'course_revenue'   => 24000.00,
    //                 //             'share_percentage' => 0.0,
    //                 //             'amount'           => 2550.00,
    //                 //             'remarks'          => null,
    //                 //             'status'           => 'pending',
    //                 //         ],
    //                 //     ],
    //                 // ],
    //             ],

    //             // Tab 2 — Released
    //             'released' => [
    //                 // [
    //                 //     'id'               => 302,
    //                 //     'teacher_id'       => 7,
    //                 //     'teacher_name'     => 'Arjun Menon',
    //                 //     'period_start'     => '2025-04-01',
    //                 //     'period_end'       => '2025-04-30',
    //                 //     'total_classes'    => 9,
    //                 //     'gross_amount'     => 13500.00,
    //                 //     'deduction_amount' => 500.00,
    //                 //     'deduction_reason' => 'One class cancelled without notice',
    //                 //     'amount'           => 13000.00,
    //                 //     'payment_method'   => 'bank_transfer',
    //                 //     'payment_reference' => null,
    //                 //     'transaction_no'   => 'NEFT20240201',
    //                 //     'payment_date'     => '2025-05-03',
    //                 //     'remarks'          => 'On time payment',
    //                 //     'status'           => 'released',
    //                 //     'paid_at'          => '2025-05-03T10:00:00.000000Z',
    //                 //     'released_by_name' => 'Admin',
    //                 //     'created_by_name'  => 'Admin',
    //                 //     'items' => [
    //                 //         [
    //                 //             'id'               => 3021,
    //                 //             'course_id'        => 1,
    //                 //             'course_name'      => 'Flutter & Dart – Complete Bootcamp',
    //                 //             'month'            => 'Apr 2025',
    //                 //             'calculation_type' => 'percentage',
    //                 //             'student_count'    => 13,
    //                 //             'course_revenue'   => 58500.00,
    //                 //             'share_percentage' => 15.0,
    //                 //             'amount'           => 8775.00,
    //                 //             'remarks'          => null,
    //                 //             'status'           => 'released',
    //                 //         ],
    //                 //         [
    //                 //             'id'               => 3022,
    //                 //             'course_id'        => 4,
    //                 //             'course_name'      => 'Python for Data Science',
    //                 //             'month'            => 'Apr 2025',
    //                 //             'calculation_type' => 'per_class',
    //                 //             'student_count'    => 8,
    //                 //             'course_revenue'   => 24000.00,
    //                 //             'share_percentage' => 0.0,
    //                 //             'amount'           => 4225.00,
    //                 //             'remarks'          => null,
    //                 //             'status'           => 'released',
    //                 //         ],
    //                 //     ],
    //                 // ],
    //                 // [
    //                 //     'id'               => 303,
    //                 //     'teacher_id'       => 7,
    //                 //     'teacher_name'     => 'Arjun Menon',
    //                 //     'period_start'     => '2025-03-01',
    //                 //     'period_end'       => '2025-03-31',
    //                 //     'total_classes'    => 10,
    //                 //     'gross_amount'     => 15000.00,
    //                 //     'deduction_amount' => 0.00,
    //                 //     'deduction_reason' => null,
    //                 //     'amount'           => 15000.00,
    //                 //     'payment_method'   => 'upi',
    //                 //     'payment_reference' => null,
    //                 //     'transaction_no'   => 'UPI20240101',
    //                 //     'payment_date'     => '2025-04-02',
    //                 //     'remarks'          => null,
    //                 //     'status'           => 'released',
    //                 //     'paid_at'          => '2025-04-02T11:30:00.000000Z',
    //                 //     'released_by_name' => 'Admin',
    //                 //     'created_by_name'  => 'Admin',
    //                 //     'items'            => [],
    //                 // ],
    //             ],
    //         ],
    //     ]);
    // }


    public function staff(Request $request)
    {
        $pendingRelease = StaffPayment::with([
            'staff:id,name',
            'createdBy:id,name',
            'releasedBy:id,name'
        ])
            ->where('status', 'pending')
            ->latest()
            ->get()
            ->map(function ($payment) {

                return [
                    'id'                => $payment->id,
                    'staff_id'          => $payment->staff_id,
                    'staff_name'        => optional($payment->staff)->name,

                    'salary_month'      => $payment->salary_month,
                    'salary_amount'     => (float) $payment->salary_amount,
                    'bonus_amount'      => (float) $payment->bonus_amount,
                    'deduction_amount'  => (float) $payment->deduction_amount,
                    'deduction_reason'  => $payment->deduction_reason,
                    'final_amount'      => (float) $payment->final_amount,

                    'payment_method'    => $payment->payment_method,
                    'transaction_no'    => $payment->transaction_no,
                    'payment_date'      => $payment->payment_date,

                    'remarks'           => $payment->remarks,
                    'status'            => $payment->status,
                    'paid_at'           => $payment->paid_at,

                    'released_by_name' => optional($payment->releasedBy)->name,
                    'created_by_name'  => optional($payment->createdBy)->name,

                    'created_at' => $payment->created_at,
                ];
            });

        $released = StaffPayment::with([
            'staff:id,name',
            'createdBy:id,name',
            'releasedBy:id,name'
        ])
            ->where('status', 'released')
            ->latest('payment_date')
            ->get()
            ->map(function ($payment) {

                return [
                    'id'                => $payment->id,
                    'staff_id'          => $payment->staff_id,
                    'staff_name'        => optional($payment->staff)->name,

                    'salary_month'      => $payment->salary_month,
                    'salary_amount'     => (float) $payment->salary_amount,
                    'bonus_amount'      => (float) $payment->bonus_amount,
                    'deduction_amount'  => (float) $payment->deduction_amount,
                    'deduction_reason'  => $payment->deduction_reason,
                    'final_amount'      => (float) $payment->final_amount,

                    'payment_method'    => $payment->payment_method,
                    'transaction_no'    => $payment->transaction_no,
                    'payment_date'      => $payment->payment_date,

                    'remarks'           => $payment->remarks,
                    'status'            => $payment->status,
                    'paid_at'           => $payment->paid_at,

                    'released_by_name' => optional($payment->releasedBy)->name,
                    'created_by_name'  => optional($payment->createdBy)->name,

                    'created_at' => $payment->created_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'pending_release' => $pendingRelease,
                'released' => $released,
            ],
        ]);
    }

    // public function staff(Request $request)
    // {
    //     return response()->json([
    //         'success' => true,
    //         'data' => [

    //             // Tab 1 — Pending release
    //             'pending_release' => [
    //                 // [
    //                 //     'id'               => 301,
    //                 //     'teacher_id'       => 7,
    //                 //     'teacher_name'     => 'Arjun Menon',
    //                 //     'period_start'     => '2025-05-01',
    //                 //     'period_end'       => '2025-05-31',
    //                 //     'total_classes'    => 8,
    //                 //     'gross_amount'     => 12000.00,
    //                 //     'deduction_amount' => 0.00,
    //                 //     'deduction_reason' => null,
    //                 //     'amount'           => 12000.00,
    //                 //     'payment_method'   => null,
    //                 //     'payment_reference' => null,
    //                 //     'transaction_no'   => null,
    //                 //     'payment_date'     => null,
    //                 //     'remarks'          => null,
    //                 //     'status'           => 'pending',
    //                 //     'paid_at'          => null,
    //                 //     'released_by_name' => null,
    //                 //     'created_by_name'  => 'Admin',
    //                 //     'items' => [
    //                 //         [
    //                 //             'id'               => 3011,
    //                 //             'course_id'        => 1,
    //                 //             'course_name'      => 'Flutter & Dart – Complete Bootcamp',
    //                 //             'month'            => 'May 2025',
    //                 //             'calculation_type' => 'percentage', // 'percentage' | 'per_class' | 'fixed'
    //                 //             'student_count'    => 14,
    //                 //             'course_revenue'   => 63000.00,
    //                 //             'share_percentage' => 15.0,
    //                 //             'amount'           => 9450.00,
    //                 //             'remarks'          => null,
    //                 //             'status'           => 'pending',
    //                 //         ],
    //                 //         [
    //                 //             'id'               => 3012,
    //                 //             'course_id'        => 4,
    //                 //             'course_name'      => 'Python for Data Science',
    //                 //             'month'            => 'May 2025',
    //                 //             'calculation_type' => 'per_class',
    //                 //             'student_count'    => 8,
    //                 //             'course_revenue'   => 24000.00,
    //                 //             'share_percentage' => 0.0,
    //                 //             'amount'           => 2550.00,
    //                 //             'remarks'          => null,
    //                 //             'status'           => 'pending',
    //                 //         ],
    //                 //     ],
    //                 // ],
    //             ],

    //             // Tab 2 — Released
    //             'released' => [
    //                 // [
    //                 //     'id'               => 302,
    //                 //     'teacher_id'       => 7,
    //                 //     'teacher_name'     => 'Arjun Menon',
    //                 //     'period_start'     => '2025-04-01',
    //                 //     'period_end'       => '2025-04-30',
    //                 //     'total_classes'    => 9,
    //                 //     'gross_amount'     => 13500.00,
    //                 //     'deduction_amount' => 500.00,
    //                 //     'deduction_reason' => 'One class cancelled without notice',
    //                 //     'amount'           => 13000.00,
    //                 //     'payment_method'   => 'bank_transfer',
    //                 //     'payment_reference' => null,
    //                 //     'transaction_no'   => 'NEFT20240201',
    //                 //     'payment_date'     => '2025-05-03',
    //                 //     'remarks'          => 'On time payment',
    //                 //     'status'           => 'released',
    //                 //     'paid_at'          => '2025-05-03T10:00:00.000000Z',
    //                 //     'released_by_name' => 'Admin',
    //                 //     'created_by_name'  => 'Admin',
    //                 //     'items' => [
    //                 //         [
    //                 //             'id'               => 3021,
    //                 //             'course_id'        => 1,
    //                 //             'course_name'      => 'Flutter & Dart – Complete Bootcamp',
    //                 //             'month'            => 'Apr 2025',
    //                 //             'calculation_type' => 'percentage',
    //                 //             'student_count'    => 13,
    //                 //             'course_revenue'   => 58500.00,
    //                 //             'share_percentage' => 15.0,
    //                 //             'amount'           => 8775.00,
    //                 //             'remarks'          => null,
    //                 //             'status'           => 'released',
    //                 //         ],
    //                 //         [
    //                 //             'id'               => 3022,
    //                 //             'course_id'        => 4,
    //                 //             'course_name'      => 'Python for Data Science',
    //                 //             'month'            => 'Apr 2025',
    //                 //             'calculation_type' => 'per_class',
    //                 //             'student_count'    => 8,
    //                 //             'course_revenue'   => 24000.00,
    //                 //             'share_percentage' => 0.0,
    //                 //             'amount'           => 4225.00,
    //                 //             'remarks'          => null,
    //                 //             'status'           => 'released',
    //                 //         ],
    //                 //     ],
    //                 // ],
    //                 // [
    //                 //     'id'               => 303,
    //                 //     'teacher_id'       => 7,
    //                 //     'teacher_name'     => 'Arjun Menon',
    //                 //     'period_start'     => '2025-03-01',
    //                 //     'period_end'       => '2025-03-31',
    //                 //     'total_classes'    => 10,
    //                 //     'gross_amount'     => 15000.00,
    //                 //     'deduction_amount' => 0.00,
    //                 //     'deduction_reason' => null,
    //                 //     'amount'           => 15000.00,
    //                 //     'payment_method'   => 'upi',
    //                 //     'payment_reference' => null,
    //                 //     'transaction_no'   => 'UPI20240101',
    //                 //     'payment_date'     => '2025-04-02',
    //                 //     'remarks'          => null,
    //                 //     'status'           => 'released',
    //                 //     'paid_at'          => '2025-04-02T11:30:00.000000Z',
    //                 //     'released_by_name' => 'Admin',
    //                 //     'created_by_name'  => 'Admin',
    //                 //     'items'            => [],
    //                 // ],
    //             ],
    //         ],
    //     ]);
    // }

    public function admin(Request $request)
    {
        // Student Renewal Pending
        $pendingCollection = $this->getPendingRenewals();

        // Teacher Pending
        $teacherPending = $this->getTeacherPayments('pending');

        // Staff Pending
        $staffPending = $this->getStaffPayments('pending');

        // Teacher Released
        $teacherReleased = $this->getTeacherPayments('released');

        // Staff Released
        $staffReleased = $this->getStaffPayments('released');

        // Student Payments
        $collected = $this->getAdmissionPayments();

        return response()->json([
            'success' => true,
            'data' => [
                'pending_collection' => $pendingCollection,

                'pending_release' => [
                    'teachers' => $teacherPending,
                    'staff'    => $staffPending,
                ],

                'released' => [
                    'teachers' => $teacherReleased,
                    'staff'    => $staffReleased,
                ],

                'collected' => $collected,
            ],
        ]);
    }



    // public function admin(Request $request)
    // {
    //     return response()->json([
    //         'success' => true,
    //         'data' => [

    //             // Tab 1 — Pending collection (student renewals not yet paid)
    //             'pending_collection' => [
    //                 // [
    //                 //     'id'                  => 601,
    //                 //     'admission_id'        => 10,
    //                 //     'student_id'          => 5,
    //                 //     'student_name'        => 'Amal Joshy',
    //                 //     'course_id'           => 1,
    //                 //     'course_name'         => 'Flutter & Dart – Complete Bootcamp',
    //                 //     'current_expiry_date' => '2025-08-10',
    //                 //     'renewal_from'        => '2025-08-11',
    //                 //     'renewal_to'          => '2025-10-10',
    //                 //     'amount'              => 4500.00,
    //                 //     'discount_amount'     => 450.00,
    //                 //     'final_amount'        => 4050.00,
    //                 //     'paid_at'             => null,
    //                 //     'status'              => 'pending',
    //                 //     'remarks'             => '10% early renewal',
    //                 //     'created_at'          => '2025-06-01T00:00:00.000000Z',
    //                 // ],
    //                 // [
    //                 //     'id'                  => 602,
    //                 //     'admission_id'        => 12,
    //                 //     'student_id'          => 6,
    //                 //     'student_name'        => 'Meera Thomas',
    //                 //     'course_id'           => 3,
    //                 //     'course_name'         => 'UI/UX Design Fundamentals',
    //                 //     'current_expiry_date' => '2025-06-02',
    //                 //     'renewal_from'        => '2025-06-02',
    //                 //     'renewal_to'          => '2025-08-02',
    //                 //     'amount'              => 3200.00,
    //                 //     'discount_amount'     => 0.00,
    //                 //     'final_amount'        => 3200.00,
    //                 //     'paid_at'             => null,
    //                 //     'status'              => 'pending',
    //                 //     'remarks'             => null,
    //                 //     'created_at'          => '2025-05-20T00:00:00.000000Z',
    //                 // ],
    //                 // [
    //                 //     'id'                  => 603,
    //                 //     'admission_id'        => 14,
    //                 //     'student_id'          => 8,
    //                 //     'student_name'        => 'Rahul Varghese',
    //                 //     'course_id'           => 4,
    //                 //     'course_name'         => 'Python for Data Science',
    //                 //     'current_expiry_date' => '2025-05-26',
    //                 //     'renewal_from'        => '2025-05-26',
    //                 //     'renewal_to'          => '2025-07-25',
    //                 //     'amount'              => 3500.00,
    //                 //     'discount_amount'     => 0.00,
    //                 //     'final_amount'        => 3500.00,
    //                 //     'paid_at'             => null,
    //                 //     'status'              => 'pending',
    //                 //     'remarks'             => 'Overdue – follow up needed',
    //                 //     'created_at'          => '2025-05-10T00:00:00.000000Z',
    //                 // ],
    //             ],

    //             // Tab 2 — Pending release
    //             'pending_release' => [

    //                 'teachers' => [
    //                     // [
    //                     //     'id'               => 301,
    //                     //     'teacher_id'       => 7,
    //                     //     'teacher_name'     => 'Arjun Menon',
    //                     //     'period_start'     => '2025-05-01',
    //                     //     'period_end'       => '2025-05-31',
    //                     //     'total_classes'    => 8,
    //                     //     'gross_amount'     => 12000.00,
    //                     //     'deduction_amount' => 0.00,
    //                     //     'amount'           => 12000.00,
    //                     //     'status'           => 'pending',
    //                     //     'paid_at'          => null,
    //                     //     'created_by_name'  => 'Admin',
    //                     //     'items'            => [],
    //                     // ],
    //                     // [
    //                     //     'id'               => 304,
    //                     //     'teacher_id'       => 9,
    //                     //     'teacher_name'     => 'Priya Nair',
    //                     //     'period_start'     => '2025-05-01',
    //                     //     'period_end'       => '2025-05-31',
    //                     //     'total_classes'    => 6,
    //                     //     'gross_amount'     => 9000.00,
    //                     //     'deduction_amount' => 0.00,
    //                     //     'amount'           => 9000.00,
    //                     //     'status'           => 'pending',
    //                     //     'paid_at'          => null,
    //                     //     'created_by_name'  => 'Admin',
    //                     //     'items'            => [],
    //                     // ],
    //                 ],

    //                 'staff' => [
    //                     // [
    //                     //     'id'               => 401,
    //                     //     'staff_id'         => 11,
    //                     //     'staff_name'       => 'Sanu Mathew',
    //                     //     'month'            => 'May 2025',
    //                     //     'salary_month'     => 'May 2025',
    //                     //     'salary_amount'    => 22000.00,
    //                     //     'bonus_amount'     => 1000.00,
    //                     //     'deduction_amount' => 0.00,
    //                     //     'deduction_reason' => null,
    //                     //     'final_amount'     => 23000.00,
    //                     //     'status'           => 'pending',
    //                     //     'paid_at'          => null,
    //                     //     'payment_method'   => null,
    //                     //     'transaction_no'   => null,
    //                     //     'payment_date'     => null,
    //                     //     'remarks'          => null,
    //                     //     'released_by_name' => null,
    //                     // ],
    //                     // [
    //                     //     'id'               => 402,
    //                     //     'staff_id'         => 12,
    //                     //     'staff_name'       => 'Divya Raj',
    //                     //     'month'            => 'May 2025',
    //                     //     'salary_month'     => 'May 2025',
    //                     //     'salary_amount'    => 18000.00,
    //                     //     'bonus_amount'     => 0.00,
    //                     //     'deduction_amount' => 500.00,
    //                     //     'deduction_reason' => 'Half-day leave x2',
    //                     //     'final_amount'     => 17500.00,
    //                     //     'status'           => 'pending',
    //                     //     'paid_at'          => null,
    //                     //     'payment_method'   => null,
    //                     //     'transaction_no'   => null,
    //                     //     'payment_date'     => null,
    //                     //     'remarks'          => null,
    //                     //     'released_by_name' => null,
    //                     // ],
    //                 ],
    //             ],

    //             // Tab 3 — Released
    //             'released' => [

    //                 'teachers' => [
    //                     // [
    //                     //     'id'               => 302,
    //                     //     'teacher_id'       => 7,
    //                     //     'teacher_name'     => 'Arjun Menon',
    //                     //     'period_start'     => '2025-04-01',
    //                     //     'period_end'       => '2025-04-30',
    //                     //     'total_classes'    => 9,
    //                     //     'gross_amount'     => 13500.00,
    //                     //     'deduction_amount' => 500.00,
    //                     //     'deduction_reason' => 'One class cancelled without notice',
    //                     //     'amount'           => 13000.00,
    //                     //     'payment_method'   => 'bank_transfer',
    //                     //     'transaction_no'   => 'NEFT20240201',
    //                     //     'payment_date'     => '2025-05-03',
    //                     //     'status'           => 'released',
    //                     //     'paid_at'          => '2025-05-03T10:00:00.000000Z',
    //                     //     'released_by_name' => 'Admin',
    //                     //     'items'            => [],
    //                     // ],
    //                     // [
    //                     //     'id'               => 305,
    //                     //     'teacher_id'       => 9,
    //                     //     'teacher_name'     => 'Priya Nair',
    //                     //     'period_start'     => '2025-04-01',
    //                     //     'period_end'       => '2025-04-30',
    //                     //     'total_classes'    => 7,
    //                     //     'gross_amount'     => 10500.00,
    //                     //     'deduction_amount' => 0.00,
    //                     //     'amount'           => 10500.00,
    //                     //     'payment_method'   => 'upi',
    //                     //     'transaction_no'   => 'UPI20240202',
    //                     //     'payment_date'     => '2025-05-04',
    //                     //     'status'           => 'released',
    //                     //     'paid_at'          => '2025-05-04T09:30:00.000000Z',
    //                     //     'released_by_name' => 'Admin',
    //                     //     'items'            => [],
    //                     // ],
    //                 ],

    //                 'staff' => [
    //                     // [
    //                     //     'id'               => 403,
    //                     //     'staff_id'         => 11,
    //                     //     'staff_name'       => 'Sanu Mathew',
    //                     //     'month'            => 'Apr 2025',
    //                     //     'salary_month'     => 'Apr 2025',
    //                     //     'salary_amount'    => 22000.00,
    //                     //     'bonus_amount'     => 0.00,
    //                     //     'deduction_amount' => 0.00,
    //                     //     'deduction_reason' => null,
    //                     //     'final_amount'     => 22000.00,
    //                     //     'status'           => 'paid',
    //                     //     'paid_at'          => '2025-05-02T10:00:00.000000Z',
    //                     //     'payment_method'   => 'bank_transfer',
    //                     //     'transaction_no'   => 'NEFT20240205',
    //                     //     'payment_date'     => '2025-05-02',
    //                     //     'remarks'          => null,
    //                     //     'released_by_name' => 'Admin',
    //                     // ],
    //                 ],
    //             ],

    //             // Tab 4 — Collected (admission payments received from students)
    //             'collected' => [
    //                 // [
    //                 //     'id'             => 1001,
    //                 //     'admission_id'   => 10,
    //                 //     'student_id'     => 5,
    //                 //     'student_name'   => 'Amal Joshy',
    //                 //     'course_id'      => 1,
    //                 //     'course_name'    => 'Flutter & Dart – Complete Bootcamp',
    //                 //     'amount'         => 4500.00,
    //                 //     'payment_method' => 'upi',
    //                 //     'transaction_no' => 'UPI2024031501',
    //                 //     'remarks'        => 'Admission fee',
    //                 //     'paid_at'        => '2025-04-23T10:30:00.000000Z',
    //                 //     'received_by'    => 'Admin',
    //                 //     'created_at'     => '2025-04-23T10:30:00.000000Z',
    //                 // ],
    //                 // [
    //                 //     'id'             => 1004,
    //                 //     'admission_id'   => 12,
    //                 //     'student_id'     => 6,
    //                 //     'student_name'   => 'Meera Thomas',
    //                 //     'course_id'      => 3,
    //                 //     'course_name'    => 'UI/UX Design Fundamentals',
    //                 //     'amount'         => 3200.00,
    //                 //     'payment_method' => 'cash',
    //                 //     'transaction_no' => null,
    //                 //     'remarks'        => null,
    //                 //     'paid_at'        => '2025-04-30T11:00:00.000000Z',
    //                 //     'received_by'    => 'Reception',
    //                 //     'created_at'     => '2025-04-30T11:00:00.000000Z',
    //                 // ],
    //                 // [
    //                 //     'id'             => 1005,
    //                 //     'admission_id'   => 14,
    //                 //     'student_id'     => 8,
    //                 //     'student_name'   => 'Rahul Varghese',
    //                 //     'course_id'      => 4,
    //                 //     'course_name'    => 'Python for Data Science',
    //                 //     'amount'         => 3500.00,
    //                 //     'payment_method' => 'bank_transfer',
    //                 //     'transaction_no' => 'NEFT20240315',
    //                 //     'remarks'        => null,
    //                 //     'paid_at'        => '2025-05-18T09:00:00.000000Z',
    //                 //     'received_by'    => 'Admin',
    //                 //     'created_at'     => '2025-05-18T09:00:00.000000Z',
    //                 // ],
    //                 // [
    //                 //     'id'             => 1006,
    //                 //     'admission_id'   => 15,
    //                 //     'student_id'     => 9,
    //                 //     'student_name'   => 'Anjali Suresh',
    //                 //     'course_id'      => 1,
    //                 //     'course_name'    => 'Flutter & Dart – Complete Bootcamp',
    //                 //     'amount'         => 4500.00,
    //                 //     'payment_method' => 'upi',
    //                 //     'transaction_no' => 'UPI20240320',
    //                 //     'remarks'        => null,
    //                 //     'paid_at'        => '2025-05-28T14:00:00.000000Z',
    //                 //     'received_by'    => 'Admin',
    //                 //     'created_at'     => '2025-05-28T14:00:00.000000Z',
    //                 // ],
    //                 // [
    //                 //     'id'             => 1007,
    //                 //     'admission_id'   => 16,
    //                 //     'student_id'     => 10,
    //                 //     'student_name'   => 'Vishnu Kumar',
    //                 //     'course_id'      => 2,
    //                 //     'course_name'    => 'Laravel REST API Masterclass',
    //                 //     'amount'         => 3800.00,
    //                 //     'payment_method' => 'upi',
    //                 //     'transaction_no' => 'UPI20240328',
    //                 //     'remarks'        => null,
    //                 //     'paid_at'        => '2025-06-05T16:00:00.000000Z',
    //                 //     'received_by'    => 'Admin',
    //                 //     'created_at'     => '2025-06-05T16:00:00.000000Z',
    //                 // ],
    //             ],
    //         ],
    //     ]);
    // }
}
