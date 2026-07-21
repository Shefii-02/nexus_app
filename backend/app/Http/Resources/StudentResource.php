<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'roll_number' => $this->student?->roll_number,

            'phone' => $this->phone,
            'address' => $this->student?->address,
            'guardian_name' => $this->student?->guardian_name,
            'guardian_phone' => $this->student?->guardian_phone,
            'name' => $this->name,
            'email' => $this->email,
            'avatar' => $this->avatar_url,
            'status'  => $this->status ?? $this->student->status,

            'created_at'=> $this->created_at,
            'last_active'=> $this->last_activation,
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
