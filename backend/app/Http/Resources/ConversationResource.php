<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->title,

            'participants' => $this->participants->map(fn($p) => [
                'id' => $p->user->id,
                'name' => $p->user->name,
            ]),
        ];
    }
}
