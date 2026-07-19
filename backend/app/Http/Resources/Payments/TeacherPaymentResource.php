<?php
namespace App\Http\Resources\Payments;
use Illuminate\Http\Resources\Json\JsonResource;

class TeacherPaymentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'               => $this->id,
            'teacher_id'       => $this->teacher_id,
            'teacher_name'     => $this->whenLoaded('teacher', fn() => $this->teacher->full_name ?? null),
            'period_start'     => $this->period_start?->toDateString(),
            'period_end'       => $this->period_end?->toDateString(),
            'total_classes'    => $this->total_classes,
            'gross_amount'     => (float) $this->gross_amount,
            'deduction_amount' => (float) $this->deduction_amount,
            'deduction_reason' => $this->deduction_reason,
            'amount'           => (float) $this->amount,
            'payment_method'   => $this->payment_method,
            'payment_reference'=> $this->payment_reference,
            'transaction_no'   => $this->transaction_no,
            'payment_date'     => $this->payment_date?->toDateString(),
            'remarks'          => $this->remarks,
            'status'           => $this->status, // pending | released
            'paid_at'          => $this->paid_at?->toIso8601String(true),
            'released_by_name' => $this->whenLoaded('releasedBy', fn() => $this->releasedBy?->name),
            'created_by_name'  => $this->whenLoaded('createdBy', fn() => $this->createdBy?->name),
            'items'            => TeacherPaymentItemResource::collection(
                $this->whenLoaded('items', fn() => $this->items)
            ),
        ];
    }
}
