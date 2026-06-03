<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            // 🧾 BASIC
            'thumbnail' => $this->thumbnailMedia
                ? asset('storage/' . $this->thumbnailMedia->file_path)
                : null,
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,

            // 📅 DATES
            'started_at' => $this->started_at,
            'ended_at' => $this->ended_at,

            // 💰 PRICING
            'actual_price' => (float) $this->actual_price,
            'net_price' => (float) $this->net_price,

            // 🎯 FLAGS
            'coupon_available' => (bool) $this->coupon_available,
            'is_renewal' => (bool) $this->is_renewal,

            // 🧑‍🏫 TYPE
            'class_type' => $this->class_type,

            // 👨‍🏫 RELATION
            'teacher_id' => $this->teacher_id,

            'teacher' => $this->whenLoaded('teacher', function () {
                return [
                    'id' => $this->teacher->user->id,
                    'name' => $this->teacher->user->name ?? null,
                    'email' => $this->teacher->user->email ?? null,
                ];
            }),

            // 💳 FEE
            'fee_type' => $this->fee_type,
            'fee_amount' => (float) $this->fee_amount,
            'duration_days' => $this->duration_days,

            // 📊 STATUS
            'status' => $this->status,

            // 🕒 META
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
