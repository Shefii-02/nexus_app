<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdmissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'student_id' => $this->student_id,

            'student' => $this->whenLoaded(
                'student',
                fn () => [
                    'id' => $this->student?->id,
                    'name' => $this->student?->name,
                    'email' => $this->student?->email,
                    'phone' => $this->student?->phone,
                ]
            ),

            'course_id' => $this->course_id,

            'course' => $this->whenLoaded(
                'course',
                fn () => [
                    'id' => $this->course?->id,
                    'title' => $this->course?->title,
                ]
            ),

            'teacher_id' => $this->teacher_id,

            'teacher' => $this->whenLoaded(
                'teacher',
                fn () => [
                    'id' => $this->teacher?->id,
                    'name' => $this->teacher?->name,
                ]
            ),

            'actual_fee' => $this->actual_fee,

            'discount_amount' => $this->discount_amount,

            'discount_reason' => $this->discount_reason,

            'coupon_id' => $this->coupon_id,

            'net_fee' => $this->net_fee,

            'admission_date' => $this->admission_date,

            'expiry_date' => $this->expiry_date,

            'status' => $this->status,

            'created_by' => $this->created_by,

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,
        ];
    }
}
