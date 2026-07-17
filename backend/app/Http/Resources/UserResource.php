<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'acc_type' => $this->acc_type,
            'status' => $this->status,
            'avatar' => $this->avatar_url,
            'parent_name' => $this->student?->parent_name,
            'role' => $this->acc_type,
            'roles' => $this->getRoleNames(),
            'permissions' => $this->getAllPermissions()->pluck('name'),
            'profile_complete' => !empty($this->email),
        ];
    }
}

