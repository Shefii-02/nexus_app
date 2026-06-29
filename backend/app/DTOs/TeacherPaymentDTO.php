<?php

namespace App\DTO;

class TeacherPaymentDTO
{
    public function __construct(
        public readonly int     $teacher_id,
        public readonly string  $period_start,
        public readonly string  $period_end,
        public readonly ?int    $total_classes,
        public readonly float   $gross_amount,
        public readonly float   $deduction_amount,
        public readonly ?string $deduction_reason,
        public readonly float   $amount,
        public readonly ?string $payment_method,
        public readonly ?string $payment_reference,
        public readonly ?string $transaction_no,
        public readonly ?string $payment_date,
        public readonly ?string $remarks,
        public readonly string  $status,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            teacher_id:        $data['teacher_id'],
            period_start:      $data['period_start'],
            period_end:        $data['period_end'],
            total_classes:     $data['total_classes']     ?? null,
            gross_amount:      $data['gross_amount'],
            deduction_amount:  $data['deduction_amount']  ?? 0,
            deduction_reason:  $data['deduction_reason']  ?? null,
            amount:            $data['amount'],
            payment_method:    $data['payment_method']    ?? null,
            payment_reference: $data['payment_reference'] ?? null,
            transaction_no:    $data['transaction_no']    ?? null,
            payment_date:      $data['payment_date']      ?? null,
            remarks:           $data['remarks']           ?? null,
            status:            $data['status']            ?? 'pending',
        );
    }

    public function toArray(): array
    {
        return [
            'teacher_id'        => $this->teacher_id,
            'period_start'      => $this->period_start,
            'period_end'        => $this->period_end,
            'total_classes'     => $this->total_classes,
            'gross_amount'      => $this->gross_amount,
            'deduction_amount'  => $this->deduction_amount,
            'deduction_reason'  => $this->deduction_reason,
            'amount'            => $this->amount,
            'payment_method'    => $this->payment_method,
            'payment_reference' => $this->payment_reference,
            'transaction_no'    => $this->transaction_no,
            'payment_date'      => $this->payment_date,
            'remarks'           => $this->remarks,
            'status'            => $this->status,
        ];
    }
}
