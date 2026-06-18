<?php

namespace App\Chat\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MessageResource extends JsonResource
{
    public function toArray(
        Request $request
    ): array {

        return [

            'id' => $this->id,

            'conversation_id' =>
                $this->conversation_id,

            'sender_id' =>
                $this->sender_id,

            'sender' =>
                $this->whenLoaded(
                    'sender',
                    fn() => [
                        'id' =>
                            $this->sender->id,
                        'name' =>
                            $this->sender->name,
                        'avatar' =>
                            $this->sender->avatar,
                    ]
                ),

            'message' =>
                $this->is_deleted
                ? null
                : $this->message,

            'type' =>
                $this->is_deleted
                ? 'deleted'
                : $this->type,

            'media' => $this->whenLoaded(
                'media',
                fn() => [
                    'id' =>
                        $this->media->id,
                    'file_name' =>
                        $this->media->file_name,
                    'file_type' =>
                        $this->media->file_type,
                    'file_size' =>
                        $this->media->file_size,
                    'file_path' =>
                        $this->media->file_path,
                    'url' =>
                        Storage::disk('public')
                            ->url(
                                $this->media->file_path
                            ),
                ]
            ),

            'media_meta' =>
                $this->is_deleted
                ? null
                : $this->media_meta,

            'reply_to' =>
                $this->reply_to,

            'is_deleted' =>
                $this->is_deleted,

            'is_edited' =>
                $this->is_edited,

            'is_pinned' =>
                $this->is_pinned,

            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }
}
