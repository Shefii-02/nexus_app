<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;



class MessageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'sender_id' => $this->sender_id,
            'message' => $this->is_deleted ? 'This message was deleted' : $this->message,
            'type' => $this->type,
            'media_url' => $this->media_url,

            'reactions' => $this->whenLoaded('reactions', function () {
                return $this->reactions->map(fn($r) => [
                    'user_id' => $r->user_id,
                    'reaction' => $r->reaction,
                ]);
            }),

            'created_at' => $this->created_at,
        ];
    }
}
