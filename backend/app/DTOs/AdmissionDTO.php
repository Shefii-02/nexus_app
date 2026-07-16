<?php

namespace App\DTOs;

use App\Models\Course;

class AdmissionDTO
{
    public function __construct(
        public int $student_id,
        public int $course_id,
        // public ?int $teacher_id,

        // public float $actual_fee,
        // public float $discount_amount,
        // public ?string $discount_reason,
        // public ?int $coupon_id,

        // public float $net_fee,

        // public string $admission_date,
        // public ?string $expiry_date,

        public string $status = 'active',

        public float $paid_amount,
        public string $payment_method,
        public ?string $transaction_no,
        public ?string $remarks,
        public ?string $notes

    ) {}

    public static function fromArray(
        array $data
    ): self {


        $course = Course::where('id',$data['course_id'])->first();

        return new self(


            student_id: (int) $data['student_id'],

            course_id: (int) $data['course_id'],

            // teacher_id: isset($data['teacher_id'])
            //     ? (int) $data['teacher_id']
            //     : null,

            // actual_fee: (float) $data['actual_fee'],

            // discount_amount: (float) (
            //     $data['discount_amount']
            //     ?? 0
            // ),

            // discount_reason: $data['discount_reason']
            //     ?? null,

            // coupon_id: isset($data['coupon_id'])
            //     ? (int) $data['coupon_id']
            //     : null,

            // net_fee: (float) $data['net_fee'],

            // admission_date: $data['admission_date'],

            // expiry_date: $data['expiry_date']
            //     ?? null,

            status: $data['status']
                ?? 'active',

            paid_amount: (float)($data['paid_amount'] ?? $course['net_price']),

            payment_method: $data['payment_method'] ?? 'upi',

            transaction_no: $data['transaction_no'] ?? null,

            remarks: $data['remarks'] ?? null,

            notes : $data['remarks'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [

            'student_id' =>
            $this->student_id,

            'course_id' =>
            $this->course_id,

            // 'teacher_id' =>
            // $this->teacher_id,

            // 'actual_fee' =>
            // $this->actual_fee,

            // 'discount_amount' =>
            // $this->discount_amount,

            // 'discount_reason' =>
            // $this->discount_reason,

            // 'coupon_id' =>
            // $this->coupon_id,

            // 'net_fee' =>
            // $this->net_fee,

            // 'admission_date' =>
            // $this->admission_date,

            // 'expiry_date' =>
            // $this->expiry_date,

            'status' =>
            $this->status,

            'created_by' =>
            auth()->id(),
        ];
    }
}
