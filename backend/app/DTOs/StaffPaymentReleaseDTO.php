<?php
namespace App\DTOs;
class StaffPaymentReleaseDTO
{
    public function __construct(
        public readonly string  $payment_method,
        public readonly ?string $transaction_no = null,
        public readonly ?string $remarks        = null,
        public readonly ?int    $released_by    = null,
    ) {}

    public static function fromRequest(array $data, int $userId): self
    {
        return new self(
            payment_method: $data['payment_method'],
            transaction_no: $data['transaction_no'] ?? null,
            remarks:        $data['remarks']        ?? null,
            released_by:    $userId,
        );
    }
}
