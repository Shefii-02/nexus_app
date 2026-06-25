<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;



class MyCourseResource extends JsonResource
{

    public function toArray($request)
    {
        $now = now();

        // resolve whichever relation is loaded
        $teacher = $this->whenLoaded('teachers', fn() => $this->teachers->first())
            ?? $this->whenLoaded('teacher', fn() => $this->teacher);

        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'description'       => $this->description,
            'teacher_name'      => $teacher?->name ?? '--',
            'teacher_avatar'    => $teacher?->avatar_url ?? null,
            'category'          => '--',
            'total_classes'     => $this->whenLoaded('classes', fn() => $this->classes->count(), 0),
            'completed_classes' => $this->whenLoaded('classes', fn() => $this->classes->where('ended_at', '<', $now)->count(), 0),
            'expires_at'        => $this->ended_at,
            'status'            => $this->status,
        ];
    }
}
