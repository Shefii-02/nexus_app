<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdmissionRenewalResource extends JsonResource
{
    public function toArray($request)
    {
        return [

            'id' => $this->id,

            'admission_id' => $this->admission_id,

            'student' => $this->student?->name,

            'course' => $this->course?->title,

            'renewal_from' => $this->renewal_from,

            'renewal_to' => $this->renewal_to,

            'amount' => $this->amount,

            'discount_amount' => $this->discount_amount,

            'final_amount' => $this->final_amount,

            'status' => $this->status,

            'paid_at' => $this->paid_at,

            'remarks' => $this->remarks,
        ];
    }
}
