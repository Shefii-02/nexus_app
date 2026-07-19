<?php
namespace App\Http\Resources\Payments;
use Illuminate\Http\Resources\Json\JsonResource;

class TeacherPaymentItemResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'               => $this->id,
            'course_id'        => $this->course_id,
            'course_name'      => $this->whenLoaded('course', fn() => $this->course->name ?? null),
            'month'            => $this->month,
            'calculation_type' => $this->calculation_type, // per_class | percentage | fixed
            'student_count'    => $this->student_count,
            'course_revenue'   => (float) $this->course_revenue,
            'share_percentage' => (float) $this->share_percentage,
            'amount'           => (float) $this->amount,
            'remarks'          => $this->remarks,
            'status'           => $this->status,
        ];
    }
}

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
            'payment_date'     => $this->payment_date?->toDateString(),
            'remarks'          => $this->remarks,
            'released_by_name' => $this->whenLoaded('releasedBy', fn() => $this->releasedBy?->name),
        ];
    }
}

// ─── PaymentReceiptResource.php ────────────────────────────────────────────

class PaymentReceiptResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'receipt_url'   => $this['receipt_url'],
            'filename'      => $this['filename'],
            'whatsapp_url'  => $this['whatsapp_url'],
            'preview_base64'=> $this['preview_base64'] ?? null,
        ];
    }
}
