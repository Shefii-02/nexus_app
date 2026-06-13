<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TeacherResource extends JsonResource
{
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'subject' => $this->teacher?->subject,
            'qualification' => $this->teacher?->qualification,
            'experience_years' => $this->teacher?->experience_years,
            'address' => $this->teacher?->address,
            'status'  => $this->status ?? $this->user,

            'user' => [
                'id' => $this->id,
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'status'  => $this->status,
                'created_at' => $this->created_at,
                'last_active' => $this->last_active,
            ]
        ];
    }
}
