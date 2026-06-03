<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TeacherResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'subject' => $this->subject,
            'qualification' => $this->qualification,
            'experience_years' => $this->experience_years,
            'address' => $this->address,
            'status'  => $this->user?->status ?? $this->user,

            'user' => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
                'email' => $this->user?->email,
                'phone' => $this->user?->phone,
                'status'  => $this->user,
                'created_at' => $this->created_at,
                'last_active' => $this->last_active,
            ]
        ];
    }
}
