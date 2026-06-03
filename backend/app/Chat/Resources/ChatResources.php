<?php
namespace App\Chat\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// ─── MessageResource ──────────────────────────────────────────────────────────
class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'conversation_id' => $this->conversation_id,
            'sender_id'       => $this->sender_id,
            'sender'          => $this->whenLoaded('sender', fn() => [
                'id'     => $this->sender->id,
                'name'   => $this->sender->name,
                'avatar' => $this->sender->avatar,
            ]),
            'message'         => $this->is_deleted ? null : $this->message,
            'type'            => $this->type,
            'media_url'       => $this->is_deleted ? null : $this->media_url,
            'media_meta'      => $this->is_deleted ? null : $this->media_meta,
            'reply_to'        => $this->reply_to,
            'reply_message'   => $this->whenLoaded('replyTo', fn() =>
                $this->replyTo ? [
                    'id'      => $this->replyTo->id,
                    'message' => $this->replyTo->message,
                    'type'    => $this->replyTo->type,
                    'sender'  => $this->replyTo->sender ? [
                        'id'   => $this->replyTo->sender->id,
                        'name' => $this->replyTo->sender->name,
                    ] : null,
                ] : null
            ),
            'is_deleted'      => $this->is_deleted,
            'is_edited'       => $this->is_edited,
            'is_pinned'       => $this->is_pinned,
            'reactions'       => $this->whenLoaded('reactions', fn() =>
                $this->reactions->map(fn($r) => [
                    'id'        => $r->id,
                    'user_id'   => $r->user_id,
                    'reaction'  => $r->reaction,
                    'user_name' => $r->user?->name,
                ])
            ),
            'reads'           => $this->whenLoaded('reads', fn() =>
                $this->reads->map(fn($r) => [
                    'user_id' => $r->user_id,
                    'read_at' => $r->read_at,
                ])
            ),
            'created_at'      => $this->created_at->toISOString(),
            'updated_at'      => $this->updated_at->toISOString(),
        ];
    }
}

// ─── ConversationResource ─────────────────────────────────────────────────────
class ConversationResource extends JsonResource
{
    public int $currentUserId;

    public function toArray(Request $request): array
    {
        $participant = $this->participants->firstWhere('user_id', $this->currentUserId ?? $request->user()?->id);

        return [
            'id'            => $this->id,
            'type'          => $this->type,
            'title'         => $this->title,
            'avatar'        => $this->avatar,
            'module_id'     => $this->module_id,
            'status'        => $this->status,
            'created_by'    => $this->created_by,
            'created_at'    => $this->created_at->toISOString(),
            'updated_at'    => $this->updated_at->toISOString(),

            // Participant-specific
            'is_muted'      => $participant?->is_muted ?? false,
            'is_pinned'     => $participant?->is_pinned ?? false,
            'unread_count'  => $this->unread_count ?? 0,

            // Other user (individual only)
            'other_user'    => $this->type === 'individual'
                ? $this->whenLoaded('participants', fn() => collect($this->participants)
                    ->firstWhere('user_id', '!=', $this->currentUserId ?? $request->user()?->id)
                    ?->user ? [
                        'id'     => collect($this->participants)->firstWhere('user_id', '!=', $this->currentUserId ?? $request->user()?->id)->user->id,
                        'name'   => collect($this->participants)->firstWhere('user_id', '!=', $this->currentUserId ?? $request->user()?->id)->user->name,
                        'avatar' => collect($this->participants)->firstWhere('user_id', '!=', $this->currentUserId ?? $request->user()?->id)->user->avatar,
                    ] : null)
                : null,

            // Participants (group)
            'participants'  => $this->whenLoaded('participants', fn() =>
                $this->participants->map(fn($p) => [
                    'id'          => $p->id,
                    'user_id'     => $p->user_id,
                    'status'      => $p->status,
                    'is_muted'    => $p->is_muted,
                    'is_pinned'   => $p->is_pinned,
                    'last_read_at'=> $p->last_read_at?->toISOString(),
                    'user'        => $p->user ? [
                        'id'     => $p->user->id,
                        'name'   => $p->user->name,
                        'avatar' => $p->user->avatar,
                        'email'  => $p->user->email,
                    ] : null,
                ])
            ),

            // Last message
            'last_message'  => $this->whenLoaded('messages', fn() =>
                $this->messages->first() ? new MessageResource($this->messages->first()) : null
            ),
        ];
    }
}
