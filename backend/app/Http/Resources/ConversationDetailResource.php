<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\MessageResource;


class ConversationDetailResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,

            'messages' => MessageResource::collection(
                $this->messages()->latest()->paginate(20)
            ),
        ];
    }
}
