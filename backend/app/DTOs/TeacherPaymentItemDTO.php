<?php

namespace App\DTOs;

class TeacherPaymentItemDTO
{
    public function __construct(

        public int $teacher_id,

        public ?int $course_id,

        public string $month,

        public string $calculation_type,

        public int $student_count,

        public float $course_revenue,

        public float $share_percentage,

        public float $amount,

        public ?string $remarks = null
    ) {}

    public static function fromArray(
        array $data
    ): self {

        return new self(

            teacher_id:
                $data['teacher_id'],

            course_id:
                $data['course_id'] ?? null,

            month:
                $data['month'],

            calculation_type:
                $data['calculation_type'],

            student_count:
                $data['student_count'] ?? 0,

            course_revenue:
                $data['course_revenue'] ?? 0,

            share_percentage:
                $data['share_percentage'] ?? 0,

            amount:
                $data['amount'],

            remarks:
                $data['remarks'] ?? null
        );
    }

    public function toArray(): array
    {
        return [

            'teacher_id' =>
                $this->teacher_id,

            'course_id' =>
                $this->course_id,

            'month' =>
                $this->month,

            'calculation_type' =>
                $this->calculation_type,

            'student_count' =>
                $this->student_count,

            'course_revenue' =>
                $this->course_revenue,

            'share_percentage' =>
                $this->share_percentage,

            'amount' =>
                $this->amount,

            'remarks' =>
                $this->remarks,

            'status' =>
                'pending',

            'created_by' =>
                auth()->id(),
        ];
    }
}
