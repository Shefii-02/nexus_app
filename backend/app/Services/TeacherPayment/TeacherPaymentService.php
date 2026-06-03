<?php

namespace App\Services\TeacherPayment;

use App\Models\Transaction;
use App\Models\TeacherPayment;
use App\Models\TeacherPaymentItem;
use Illuminate\Support\Facades\DB;

class TeacherPaymentService
{
    public function release(
        array $itemIds,
        string $paymentMethod,
        ?string $transactionNo = null,
        ?string $remarks = null
    ) {

        return DB::transaction(
            function () use (
                $itemIds,
                $paymentMethod,
                $transactionNo,
                $remarks
            ) {

                $items =
                    TeacherPaymentItem::whereIn(
                        'id',
                        $itemIds
                    )
                    ->where(
                        'status',
                        'pending'
                    )
                    ->get();

                $teacherId =
                    $items
                    ->first()
                    ->teacher_id;

                $total =
                    $items
                    ->sum('amount');

                $payment =
                    TeacherPayment::create([

                        'teacher_id' =>
                        $teacherId,

                        'amount' =>
                        $total,

                        'payment_method' =>
                        $paymentMethod,

                        'transaction_no' =>
                        $transactionNo,

                        'payment_date' =>
                        now(),

                        'remarks' =>
                        $remarks,

                        'released_by' =>
                        auth()->id(),
                    ]);

                $payment
                    ->items()
                    ->attach(
                        $items->pluck('id')
                    );

                TeacherPaymentItem::whereIn(
                    'id',
                    $itemIds
                )
                    ->update([
                        'status' =>
                        'released'
                    ]);

                Transaction::create([

                    'type' =>
                    'expense',

                    'category' =>
                    'teacher_payment',

                    'reference_type' =>
                    'teacher_payment',

                    'reference_id' =>
                    $payment->id,

                    'amount' =>
                    $total,

                    'payment_method' =>
                    $paymentMethod,

                    'transaction_no' =>
                    $transactionNo,

                    'transaction_date' =>
                    now(),

                    'description' =>
                    'Teacher payment release',

                    'created_by' =>
                    auth()->id(),
                ]);

                return $payment;
            }
        );
    }


}
