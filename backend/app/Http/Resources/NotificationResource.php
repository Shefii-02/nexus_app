<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->title,
            'message' => $this->message,
            'priority' => $this->priority,
            'is_read' => (bool) $this->read_at,
            'read_at' => $this->read_at,
            'related_model' => $this->related_model,
            'related_id' => $this->related_id,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'status'     => 'processing',
            'total_receivers' => $this->total_receivers,
            'scheduled_at' => $this->scheduled_at

        ];
    }
}
