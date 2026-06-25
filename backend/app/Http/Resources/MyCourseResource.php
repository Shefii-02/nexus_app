<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;



class MyCourseResource extends JsonResource
{
    public function toArray($request)
    {
        $now = now();
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'description'       => $this->description,
            'teacher_name'      => $this->teacher?->name ?? '--',
            'teacher_avatar'    => $this->teacher?->avatar_url,
            'category'          => '--',
            'total_classes'     => $this->classes?->count(),
            'completed_classes' => $this->classes?->where('ended_at', '<', $now)->count(),
            'expires_at'        => $this->ended_at, // date('Y-m-d') to  '2025-08-10T00:00:00.000000Z',
            'status'            => $this->status,
        ];
    }
}
