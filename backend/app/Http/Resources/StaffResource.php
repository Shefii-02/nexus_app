<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StaffResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'department' => $this->department,
            'designation' => $this->designation,
            'phone' => $this->phone,
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
