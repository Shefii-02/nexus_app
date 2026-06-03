<?php

namespace App\DTOs;

class RenewalDTO
{
    public function __construct(
        public int $student_id,
        public int $course_id,
        public float $amount,
        public string $renewal_date,
        public ?string $payment_reference,
        public ?string $notes,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['student_id'],
            $data['course_id'],
            $data['amount'],
            $data['renewal_date'],
            $data['payment_reference'] ?? null,
            $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            ...get_object_vars($this),
            'status' => 'paid'
        ];
    }
}
