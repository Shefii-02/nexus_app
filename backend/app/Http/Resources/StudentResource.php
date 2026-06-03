<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'roll_number' => $this->roll_number,
            'phone' => $this->phone,
            'address' => $this->address,
            'guardian_name' => $this->guardian_name,
            'guardian_phone' => $this->guardian_phone,
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
