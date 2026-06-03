<?php

namespace App\DTOs;

class AdmissionRenewalDTO
{
    public function __construct(
        public int $admission_id,
        public float $amount,
        public float $discount_amount = 0,
        public float $final_amount = 0,
        public ?string $remarks = null,
        public string $status = 'pending'
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            admission_id: $data['admission_id'],
            amount: $data['amount'],
            discount_amount: $data['discount_amount'] ?? 0,
            final_amount: $data['final_amount'],
            remarks: $data['remarks'] ?? null,
            status: $data['status'] ?? 'pending'
        );
    }
}
