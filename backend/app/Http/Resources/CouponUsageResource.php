<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CouponUsageResource extends JsonResource
{
    public function toArray($request)
    {
        return [

            'id' => $this->id,

            'coupon' => [
                'id' => $this->coupon?->id,
                'code' => $this->coupon?->code,
            ],

            'user' => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
            ],

            'admission_id' =>
                $this->admission_id,

            'renewal_id' =>
                $this->renewal_id,

            'original_amount' =>
                $this->original_amount,

            'discount_amount' =>
                $this->discount_amount,

            'final_amount' =>
                $this->final_amount,

            'created_at' =>
                $this->created_at,
        ];
    }
}
