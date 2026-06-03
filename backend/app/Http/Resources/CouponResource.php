<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    public function toArray($request)
    {
        return [

            'id' => $this->id,

            'code' => $this->code,

            'title' => $this->title,

            'description' => $this->description,

            'discount_type' => $this->discount_type,

            'discount_value' => $this->discount_value,

            'max_discount_amount' => $this->max_discount_amount,

            'minimum_amount' => $this->minimum_amount,

            'usage_limit' => $this->usage_limit,

            'usage_per_user' => $this->usage_per_user,

            'start_date' => $this->start_date,

            'end_date' => $this->end_date,

            'apply_on' => $this->apply_on,

            'is_active' => $this->is_active,

            'total_usage' =>
                $this->whenLoaded(
                    'usages',
                    fn() => $this->usages->count()
                ),

            'created_at' => $this->created_at,
        ];
    }
}
