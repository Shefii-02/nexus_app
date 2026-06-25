<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class MyCourseDetailResource extends JsonResource
{
    public function toArray($request)
    {
        $now     = now();
        $teacher = $this->teachers->first();



        return [
            'course'    => [
                'id'                => $this->id,
                'name'              => $this->name,
                'description'       => $this->description,
                'teacher_name'      => $teacher?->name ?? '--',
                'teacher_avatar'    => $teacher?->avatar_url ?? null,
                'category'          => '--',
                'total_classes'     => $this->classes->count(),
                'completed_classes' => $this->classes->where('status', 'completed')->count(),
                'expires_at'        => $this->ended_at,
                'status'            => $this->status,
            ],
            'classes'   => $this->classes->map(fn($class) => [
                'id'               => $class->id,
                'title'            => $class->title,
                'description'      => $class->description,
                'scheduled_at'     => $class->scheduled_date,
                'duration_minutes' => $class->duration_minutes,
                'status'           => $this->resolveClassStatus($class),         // live | upcoming | completed
                'meeting_url'      => $class->class_link,
                'recording_url'    => $class->record_link,
                'teacher_name'     => $teacher?->name ?? '--',
                'attendance_count' => 0,                        // wire up when attendance table exists
            ]),
            'materials' => $this->materials->map(fn($mat) => [
                'id'           => $mat->id,
                'title'        => $mat->title,
                'description'  => $mat->description,
                'type'         => $mat->material_type,
                'file_extension' => pathinfo($mat->file_url, PATHINFO_EXTENSION) ?: $mat->material_type,
                'file_url'     => $mat->file_url,
                'file_size_mb' => null,                         // add column if needed
                'uploaded_at'  => $mat->created_at,
                'uploaded_by'  => $teacher?->name ?? '--',
            ]),
        ];
    }

    private function resolveClassStatus($class): string
    {
        if ($class->status != 'active') {
            return 'upcoming';
        }

        $now   = Carbon::now();
        $start = Carbon::parse($class->started_at);
        $end   = Carbon::parse($class->ended_at);

        if ($now->gt($end))                  return 'completed';
        if ($now->lt($start)) return 'upcoming';
        if ($now->between($start, $end))     return 'live';

        return 'upcoming';
    }
}
