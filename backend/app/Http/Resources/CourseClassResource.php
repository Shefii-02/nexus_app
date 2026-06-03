<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseClassResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            // 🔹 RELATIONS
            'course_id' => $this->course_id,
            'teacher_id' => $this->teacher_id,

            'course' => $this->whenLoaded('course', function () {
                return [
                    'id' => $this->course->id,
                    'name' => $this->course->name,
                    'code' => $this->course->code,
                ];
            }),

            'teacher' => $this->whenLoaded('teacher', function () {
                return [
                    'id' => $this->teacher->id,
                    'name' => $this->teacher->user->name ?? null,
                    'email' => $this->teacher->user->email ?? null,
                ];
            }),

            // 🔹 BASIC
            'title' => $this->title,
            'description' => $this->description,

            // 🔹 LINKS
            'class_link' => $this->class_link,
            'record_link' => $this->record_link,

            // 🔹 SOURCE
            'source' => $this->source,

            // 🔹 CLASS INFO
            'class_number' => $this->class_number,
            'scheduled_date' => $this->scheduled_date?->format('Y-m-d H:i:s'),
            'started_at' => date('Y-m-d H:i:s', strtotime($this->started_at)),
            'ended_at' => date('Y-m-d H:i:s', strtotime($this->ended_at)),
            'duration_minutes' => $this->duration_minutes,
            'room_location' => $this->room_location,

            // 🔹 STATUS
            'status' => $this->status,

            // 🔹 META
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
