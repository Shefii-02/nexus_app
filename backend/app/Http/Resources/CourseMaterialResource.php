<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseMaterialResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            // 🔹 RELATION
            'course_id' => $this->course_id,

            'course' => $this->whenLoaded('course', function () {
                return [
                    'id' => $this->course->id,
                    'name' => $this->course->name,
                    'code' => $this->course->code,
                ];
            }),

            // 🔹 BASIC
            'title' => $this->title,
            'description' => $this->description,

            // 🔹 FILE
             'file_url' => $this->media
                ? asset('storage/' . $this->media->file_path)
                : null,

            'material_type' => $this->material_type,

            // 🔹 ORDER
            'order' => $this->order,

            // 🔹 STATUS
            'status' => $this->status,

            // 🔹 META
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),

            'conversation' => $this->conversation,
        ];
    }
}
