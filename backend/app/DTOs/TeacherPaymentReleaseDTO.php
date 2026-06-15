<?php

namespace App\DTOs;

class TeacherPaymentReleaseDTO
{
    public function __construct(
        public readonly string  $payment_method,
        public readonly ?string $payment_reference = null,
        public readonly ?string $remarks           = null,
        public readonly ?int    $released_by       = null,
    ) {}

    public static function fromRequest(array $data, int $userId): self
    {
        return new self(
            payment_method:    $data['payment_method'],
            payment_reference: $data['payment_reference'] ?? null,
            remarks:           $data['remarks']           ?? null,
            released_by:       $userId,
        );
    }
}


