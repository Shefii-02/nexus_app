<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdmissionPaymentResource
extends JsonResource
{
    public function toArray($request)
    {
        return [

            'id' => $this->id,

            'amount' => $this->amount,

            'payment_method' =>
                $this->payment_method,

            'transaction_no' =>
                $this->transaction_no,

            'remarks' =>
                $this->remarks,

            'paid_at' =>
                $this->paid_at,

            'student' =>
                $this->student?->name,

            'course' =>
                $this->course?->title,
        ];
    }
}
