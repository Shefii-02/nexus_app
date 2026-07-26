<?php

namespace App\Chat\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $request->user();

        $activeParticipants = $this->participants->where('status', 'active');

        return [
            'id'               => $this->id,
            'title'            => $this->title,
            'avatar'           => $this->resolveAvatar($user, $activeParticipants),
            'type'             => $this->type ?? 'single',
            'status'           => $this->status,
            'reply_permission' => $this->reply_permission,
            'created_by'       => $this->created_by,
            'total_members'    => $activeParticipants->count(),

            'participants' => $activeParticipants->map(fn ($p) => [
                'id'         => $p->user->id,
                'name'       => $p->user->name,
                'phone'      => $p->user->phone,
                'avatar'     => $p->user->avatar_url ?? null,
                'role'       => $p->user->acc_type,
                'is_creator' => $p->user->id === $this->created_by,
            ])->values(),
        ];
    }

    /**
     * Group chats: use the conversation's own uploaded avatar column/relation.
     * Single chats: use the OTHER participant's user avatar.
     */
    private function resolveAvatar($user, $activeParticipants): ?string
    {
        if ($this->type === 'group') {
            return $this->media_url;
        }

        $other = $activeParticipants->first(fn ($p) => $p->user_id !== $user->id);

        return $other?->user?->avatar_url ?? null;
    }
}
