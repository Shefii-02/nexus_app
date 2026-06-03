<?php

namespace App\DTOs;

class PaymentDTO
{
    public function __construct(
        public int $student_id,
        public int $course_id,
        public float $amount,
        public string $payment_method,
        public ?string $reference_number,
        public ?string $notes,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['student_id'],
            $data['course_id'],
            $data['amount'],
            $data['payment_method'],
            $data['reference_number'] ?? null,
            $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            ...get_object_vars($this),
            'payment_date' => now(),
            'status' => 'paid'
        ];
    }
}
