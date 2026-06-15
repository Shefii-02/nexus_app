<?php

namespace App\DTOs;

class TransactionDTO
{
    public function __construct(
        public readonly string  $type,
        public readonly ?string $category,
        public readonly float   $amount,
        public readonly string  $payment_method,
        public readonly string  $transaction_date,
        public readonly ?string $description     = null,
        public readonly ?string $reference_type  = null,
        public readonly ?string $reference_id    = null,
        public readonly ?int    $created_by      = null,
    ) {}

    public static function fromRequest(array $data, int $userId): self
    {
        return new self(
            type:             $data['type'],
            category:         $data['category']         ?? null,
            amount:           (float) $data['amount'],
            payment_method:   $data['payment_method'],
            transaction_date: $data['transaction_date'],
            description:      $data['description']      ?? null,
            reference_type:   $data['reference_type']   ?? null,
            reference_id:     $data['reference_id']     ?? null,
            created_by:       $userId,
        );
    }

    public function toArray(): array
    {
        return [
            'type'             => $this->type,
            'category'         => $this->category,
            'amount'           => $this->amount,
            'payment_method'   => $this->payment_method,
            'transaction_date' => $this->transaction_date,
            'description'      => $this->description,
            'reference_type'   => $this->reference_type,
            'reference_id'     => $this->reference_id,
            'created_by'       => $this->created_by,
        ];
    }
}
