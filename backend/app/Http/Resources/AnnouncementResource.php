<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AnnouncementResource extends JsonResource
{
    public function toArray($req)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'thumbnail' => $this->thumbnailMedia
                ? asset('storage/' . $this->thumbnailMedia->file_path)
                : null,
            'target_type' => $this->target_type,
            'priority' => $this->priority,
            'status' => $this->status,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
        ];
    }
}
