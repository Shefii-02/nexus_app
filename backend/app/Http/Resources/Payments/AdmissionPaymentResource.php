<?php

namespace App\Http\Resources\Payments;

use Illuminate\Http\Resources\Json\JsonResource;

class AdmissionPaymentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'             => $this->id,
            'admission_id'   => $this->admission_id,
            'student_id'     => $this->student_id,
            'student_name'   => $this->whenLoaded('student', fn() => $this->student->full_name ?? null),
            'course_id'      => $this->course_id,
            'course_name'    => $this->whenLoaded('course', fn() => $this->course->name ?? null),
            'amount'         => (float) $this->amount,
            'payment_method' => $this->payment_method,
            'transaction_no' => $this->transaction_no,
            'remarks'        => $this->remarks,
            'paid_at'        => $this->paid_at?->toISOString(),
            'received_by'    => $this->whenLoaded('receivedBy', fn() => $this->receivedBy?->name),
            'created_at'     => $this->created_at?->toISOString(),
        ];
    }
}
