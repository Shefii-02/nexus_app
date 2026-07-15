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
                    $this->sender->avatar_url,
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

            'media_url' => $this->media?->file_path ?  Storage::disk('public')->url($this->media->file_path) : "",

            // 'media_url' => $this->whenLoaded(
            //     'media',
            //     fn() => [
            //         'id' =>
            //             $this->media->id,
            //         'file_name' =>
            //             $this->media->file_name,
            //         'file_type' =>
            //             $this->media->file_type,
            //         'file_size' =>
            //             $this->media->file_size,
            //         'file_path' =>
            //             $this->media->file_path,
            //         'url' =>
            //             Storage::disk('public')
            //                 ->url(
            //                     $this->media->file_path
            //                 ),
            //     ]
            // ),

            // 'media_meta' =>
            //     $this->is_deleted
            //     ? null
            //     : $this->media_meta,

            // 'reply_to' =>
            // $this->reply_to,

            'reply_to'        => $this->reply_to,
            'reply_to_message' => $this->whenLoaded(
                'replyTo',
                fn() =>
                $this->replyTo ? [
                    'id'              => $this->replyTo?->id,
                    'conversation_id' => $this->replyTo?->conversation_id,
                    'sender_id'       => $this->replyTo?->sender_id,
                    'message'         => $this->replyTo?->message,
                    'type'            => $this->replyTo?->type,
                    'media_url'       => $this->replyTo?->media_url,
                    'is_deleted'      => $this->replyTo?->is_deleted,
                    'created_at'      => $this->replyTo?->created_at?->toISOString(),
                    'sender'          => $this->replyTo?->sender ? [
                        'id'   => $this->replyTo?->sender->id,
                        'name' => $this->replyTo?->sender->name,
                    ] : null,
                ] : null
            ),

            'is_deleted' =>
            $this->is_deleted,

            'is_edited' =>
            $this->is_edited,

            'is_pinned' =>
            $this->is_pinned,
            'reactions'       => $this->whenLoaded(
                'reactions',
                fn() =>
                $this->reactions->map(fn($r) => [
                    'id'        => $r->id,
                    'user_id'   => $r->user_id,
                    'reaction'  => $r->reaction,
                    'user_name' => $r->user?->name,
                ])
            ),
            'created_at' =>
            $this->created_at?->toISOString(),

            'updated_at' =>
            $this->updated_at?->toISOString(),
        ];
    }
}


// ─── MessageResource ──────────────────────────────────────────────────────────
// class MessageResource extends JsonResource
// {
//     public function toArray(Request $request): array
//     {
//         return [
//             'id'              => $this->id,
//             'conversation_id' => $this->conversation_id,
//             'sender_id'       => $this->sender_id,
//             'sender'          => $this->whenLoaded('sender', fn() => [
//                 'id'     => $this->sender->id,
//                 'name'   => $this->sender->name,
//                 'avatar' => $this->sender->avatar,
//             ]),
//             'message'         => $this->is_deleted ? null : $this->message,
//             // 'type'            => $this->type,
//             'type' => $this->is_deleted ? 'deleted' : $this->type,

//             'media_url'       => $this->is_deleted ? null : $this->media_url,
//             // 'media_meta'      => $this->is_deleted ? null : $this->media_meta,
//             'media_meta' => $this->is_deleted
//                 ? null
//                 : $this->media_meta,
//             'reply_to'        => $this->reply_to,
//             'reply_to_message' => $this->whenLoaded(
//                 'replyTo',
//                 fn() =>
//                 $this->replyTo ? [
//                     'id'              => $this->replyTo->id,
//                     'conversation_id' => $this->replyTo->conversation_id,
//                     'sender_id'       => $this->replyTo->sender_id,
//                     'message'         => $this->replyTo->message,
//                     'type'            => $this->replyTo->type,
//                     'media_url'       => $this->replyTo->media_url,
//                     'is_deleted'      => $this->replyTo->is_deleted,
//                     'created_at'      => $this->replyTo->created_at?->toISOString(),
//                     'sender'          => $this->replyTo->sender ? [
//                         'id'   => $this->replyTo->sender->id,
//                         'name' => $this->replyTo->sender->name,
//                     ] : null,
//                 ] : null
//             ),
//             'is_deleted'      => $this->is_deleted,
//             'is_edited'       => $this->is_edited,
//             'is_pinned'       => $this->is_pinned,
//             'reactions'       => $this->whenLoaded(
//                 'reactions',
//                 fn() =>
//                 $this->reactions->map(fn($r) => [
//                     'id'        => $r->id,
//                     'user_id'   => $r->user_id,
//                     'reaction'  => $r->reaction,
//                     'user_name' => $r->user?->name,
//                 ])
//             ),
//             'reads'           => $this->whenLoaded(
//                 'reads',
//                 fn() =>
//                 $this->reads->map(fn($r) => [
//                     'user_id' => $r->user_id,
//                     'read_at' => $r->read_at,
//                 ])
//             ),
//             'created_at'      => $this->created_at->toISOString(),
//             'updated_at'      => $this->updated_at->toISOString(),
//         ];
//     }
// }
