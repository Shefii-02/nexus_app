<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CallStudentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->student?->id,
            'name' => $this->student?->name,
            'email' => $this->student?->email,
            'mobile' => $this->student?->phone,
            'avatar' => $this->student?->avatar_url,
        ];
    }
}
