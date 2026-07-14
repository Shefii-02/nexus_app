<?php

namespace App\DTOs;

class StaffPaymentDTO
{
    public function __construct(

        public int $staff_id,

        public string $salary_month,

        public float $salary_amount,

        public float $bonus_amount = 0,
        public float $final_amount = 0,

        public float $deduction_amount = 0,

        public ?string $deduction_reason,


        public readonly ?string $payment_method,
        public readonly ?string $payment_reference,
        public readonly ?string $transaction_no,
        public readonly ?string $payment_date,
        public readonly ?string $remarks,
        public readonly string  $status,
        public int $created_by,
        public ?int $released_by,

    ) {}

    public static function fromArray(
        array $data
    ): self {

        return new self(

            staff_id: $data['staff_id'],

            salary_month: date('Y-m-d', strtotime($data['salary_month'])),

            salary_amount: $data['salary_amount'] ?? 0,

            bonus_amount: $data['bonus_amount'] ?? 0,

            deduction_amount: $data['deduction_amount'] ?? 0,
            deduction_reason: $data['deduction_reason'],

            final_amount: $data['final_amount'],

            remarks: $data['remarks'] ?? null,

            payment_method: $data['payment_method'],
            payment_reference: $data['payment_reference'],
            transaction_no: $data['transaction_no'],
            payment_date: $data['payment_date'],
            status: $data['status'],
            created_by: auth()->id(),
            released_by : $data['status'] == 'released' ? auth()->id() : null,

        );
    }

    public function toArray(): array
    {
        return [

            'staff_id' =>
            $this->staff_id,

            'salary_month' =>
            $this->salary_month,

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
            $this->status,

            'deduction_reason' =>
            $this->deduction_reason,
            'payment_method' =>
            $this->payment_method,
            'payment_reference' =>
            $this->payment_reference,
            'transaction_no' =>
            $this->transaction_no,
            'payment_date' =>
            $this->payment_date,
            'created_by' => auth()->id(),

            'released_by' => $this->released_by,

        ];
    }
}
