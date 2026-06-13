<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdmissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'student_id' => $this->student_id,

            'student' => $this->whenLoaded(
                'student',
                fn() => [
                    'id' => $this->student?->id,
                    'name' => $this->student?->name,
                    'email' => $this->student?->email,
                    'phone' => $this->student?->phone,
                ]
            ),

            'student_name' => $this->student?->name,
            'course_name' => $this->course?->name,

            'course_id' => $this->course_id,

            'course' => $this->whenLoaded(
                'course',
                fn() => [
                    'id' => $this->course?->id,
                    'name' => $this->course?->name,
                    'actual_price' => $this->course?->actual_price,
                    'fee_type' => $this->course?->fee_type,
                    'duration_days' => $this->course?->duration_days,
                    'is_renewal' => $this->course?->is_renewal,
                ]
            ),

            'teacher_id' => $this->teacher_id,

            'teacher' => $this->whenLoaded(
                'teacher',
                fn() => [
                    'id' => $this->teacher?->id,
                    'name' => $this->teacher?->name,
                ]
            ),

            'actual_fee' => $this->actual_fee,

            'discount_amount' => $this->discount_amount,

            'discount_reason' => $this->discount_reason,

            'coupon_id' => $this->coupon_id,

            'net_fee' => $this->net_fee,

            'total_paid' =>
            $this->payments->sum('amount'),

            'balance_amount' =>
            $this->net_fee -
                $this->payments->sum('amount'),

            'payments' => $this->whenLoaded(
                'payments',
                fn() => $this->payments->map(
                    fn($payment) => [
                        'id' => $payment->id,
                        'amount' => $payment->amount,
                        'payment_method' => $payment->payment_method,
                        'transaction_no' => $payment->transaction_no,
                        'remarks' => $payment->remarks,
                        'paid_at' => $payment->paid_at,
                    ]
                )
            ),

            'admission_date' => $this->admission_date,

            'expiry_date' => $this->expiry_date,

            'status' => $this->status,

            'created_by' => $this->created_by,

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,
        ];
    }
}
