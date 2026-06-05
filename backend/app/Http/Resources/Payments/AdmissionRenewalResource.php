<?php
namespace App\Http\Resources\Payments;
use Illuminate\Http\Resources\Json\JsonResource;

class AdmissionRenewalResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                  => $this->id,
            'admission_id'        => $this->admission_id,
            'student_id'          => $this->student_id,
            'student_name'        => $this->whenLoaded('student', fn() => $this->student->full_name ?? null),
            'course_id'           => $this->course_id,
            'course_name'         => $this->whenLoaded('course', fn() => $this->course->name ?? null),
            'current_expiry_date' => $this->current_expiry_date?->toDateString(),
            'renewal_from'        => $this->renewal_from?->toDateString(),
            'renewal_to'          => $this->renewal_to?->toDateString(),
            'amount'              => (float) $this->amount,
            'discount_amount'     => (float) $this->discount_amount,
            'final_amount'        => (float) $this->final_amount,
            'paid_at'             => $this->paid_at?->toISOString(),
            'status'              => $this->status, // pending | paid | expired
            'remarks'             => $this->remarks,
            'created_at'          => $this->created_at?->toISOString(),
        ];
    }
}
