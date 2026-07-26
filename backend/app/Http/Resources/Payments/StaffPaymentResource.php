<?php
namespace App\Http\Resources\Payments;
use Illuminate\Http\Resources\Json\JsonResource;
// ─── StaffPaymentResource.php ──────────────────────────────────────────────

class StaffPaymentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'               => $this->id,
            'staff_id'         => $this->staff_id,
            'staff_name'       => $this->whenLoaded('staff', fn() => $this->staff->full_name ?? null),
            'month'            => $this->month,
            'salary_month'     => $this->salary_month,
            'salary_amount'    => (float) $this->salary_amount,
            'bonus_amount'     => (float) $this->bonus_amount,
            'deduction_amount' => (float) $this->deduction_amount,
            'deduction_reason' => $this->deduction_reason,
            'final_amount'     => (float) $this->final_amount,
            'status'           => $this->status, // pending | paid
            'paid_at'          => $this->paid_at?->toIso8601String(true),
            'payment_method'   => $this->payment_method,
            'transaction_no'   => $this->transaction_no,
            'payment_date'     => $this->payment_date,
            'remarks'          => $this->remarks,
            'released_by_name' => $this->whenLoaded('releasedBy', fn() => $this->releasedBy?->name),
        ];
    }
}
