<?php

namespace App\DTOs;

class StaffPaymentDTO
{
    public function __construct(

        public int $staff_id,

        public string $month,

        public float $salary_amount,

        public float $bonus_amount = 0,

        public float $deduction_amount = 0,

        public float $final_amount = 0,

        public ?string $remarks = null
    ) {}

    public static function fromArray(
        array $data
    ): self {

        return new self(

            staff_id:
                $data['staff_id'],

            month:
                $data['month'],

            salary_amount:
                $data['salary_amount'],

            bonus_amount:
                $data['bonus_amount'] ?? 0,

            deduction_amount:
                $data['deduction_amount'] ?? 0,

            final_amount:
                $data['final_amount'],

            remarks:
                $data['remarks'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [

            'staff_id' =>
                $this->staff_id,

            'month' =>
                $this->month,

            'salary_amount' =>
                $this->salary_amount,

            'bonus_amount' =>
                $this->bonus_amount,

            'deduction_amount' =>
                $this->deduction_amount,

            'final_amount' =>
                $this->final_amount,

            'remarks' =>
                $this->remarks,

            'status' =>
                'pending',
        ];
    }
}
