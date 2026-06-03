<?php

namespace App\DTOs;

class AdmissionPaymentDTO
{
    public function __construct(
        public int $admission_id,
        public float $amount,
        public string $payment_method,
        public ?string $transaction_no = null,
        public ?string $remarks = null
    ) {}

    public static function fromArray(
        array $data
    ): self {

        return new self(

            admission_id:
                $data['admission_id'],

            amount:
                $data['amount'],

            payment_method:
                $data['payment_method'],

            transaction_no:
                $data['transaction_no'] ?? null,

            remarks:
                $data['remarks'] ?? null,
        );
    }
}
