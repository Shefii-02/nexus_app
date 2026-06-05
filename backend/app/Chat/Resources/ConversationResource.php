<?php

namespace App\Chat\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


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
            'participants'  => $this->whenLoaded(
                'participants',
                fn() =>
                $this->participants->map(fn($p) => [
                    'id'          => $p->id,
                    'user_id'     => $p->user_id,
                    'status'      => $p->status,
                    'is_muted'    => $p->is_muted,
                    'is_pinned'   => $p->is_pinned,
                    'last_read_at' => $p->last_read_at?->toISOString(),
                    'user'        => $p->user ? [
                        'id'     => $p->user->id,
                        'name'   => $p->user->name,
                        'avatar' => $p->user->avatar,
                        'email'  => $p->user->email,
                    ] : null,
                ])
            ),

            // Last message
            'last_message'  => $this->whenLoaded(
                'messages',
                fn() =>
                $this->messages->first() ? new MessageResource($this->messages->first()) : null
            ),
        ];
    }
}
