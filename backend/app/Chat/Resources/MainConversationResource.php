<?php

namespace App\Chat\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MainConversationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $request->user();

        return [
            'id'          => $this->id,
            'type'        => $this->type ?? 'single',
            'module_id'   => $this->module_id ?? null,
            'name'        => $this->name,
            'status'      => $this->status,
            'created_by'  => $this->created_by,

            // ── Avatar resolution ────────────────────────────────────
            // Group chats: pull from the conversation's own media/avatar row.
            // Single chats: pull from the OTHER participant's user avatar.
            // 'avatar'      => $this->resolveAvatar(),
            'avatar' => $this->type === 'group'
                ? $this->resolveAvatar()
                : $this->other_user?->avatar_url ?? '',

            // ── Computed per-request fields (set on the model in the
            //    controller before wrapping, since these need the
            //    authenticated user's context) ─────────────────────────
            'unread_count'          => $this->unread_count ?? 0,
            'is_muted'              => $this->is_muted ?? false,
            'is_pinned'             => $this->is_pinned ?? false,
            'reply_permission'      => $this->reply_permission ?? 0,
            'reply_permission_value' => $this->reply_permission_value,
            'total_members'         => $this->total_members ?? 0,

            'other_user' => $this->when(
                $this->type === 'single' && $this->other_user,
                fn() => [
                    'id'     => $this->other_user->id,
                    'name'   => $this->other_user->name,
                    'phone'  => $this->other_user->phone,
                    'email'  => $this->other_user->email,
                    'avatar' => $this->other_user->avatar_url,
                    'role'   => $this->other_user->acc_type,
                ]
            ),

            'participants' => $this->whenLoaded(
                'participants',
                fn() =>
                $this->participants
                    ->where('status', 'active')
                    ->map(fn($p) => [
                        'id'         => $p->user->id,
                        'name'       => $p->user->name,
                        'phone'      => $p->user->phone,
                        'email'      => $p->user->email,
                        'avatar'     => $p->user->avatar_url,
                        'role'       => $p->user->acc_type,
                        'is_creator' => $p->user->id === $this->created_by,
                    ])
                    ->values()
            ),

            'last_message' => $this->whenLoaded(
                'messages',
                fn() =>
                $this->messages->first()
                    ? new MessageResource($this->messages->first())
                    : null
            ),

            'created_at' => $this->created_at?->toIso8601String(true),
            'updated_at' => $this->updated_at?->toIso8601String(true),
        ];
    }

    /**
     * Group chats use the conversation's own uploaded avatar (media relation).
     * Single chats fall back to the other participant's user avatar.
     */
    // private function resolveAvatar(): ?string
    // {
    //     if ($this->type === 'group') {
    //         // Adjust relation/column name to match your schema —
    //         // assumes Conversation::media() is a hasOne/morphOne to a
    //         // media row storing the uploaded group photo.
    //         if ($this->relationLoaded('media') && $this->media) {
    //             return asset('storage/' . $this->media->file_path);
    //         }

    //         // Fallback if you store it directly as a column instead
    //         if ($this->group_avatar) {
    //             return asset('storage/' . $this->group_avatar);
    //         }

    //         return null;
    //     }

    //     // Single chat -> other user's avatar
    //     return $this->other_user?->avatar_url;
    // }
    private function resolveAvatar(): ?string
    {
        if ($this->type === 'group') {
            // if ($this->relationLoaded('media') && $this->media) {

                // return asset('storage/' . $this->media->file_path);
            // }

            return $this->media_url; // uses Conversation::getMediaUrlAttribute()


            if ($this->group_avatar) {   // ← doesn't exist, always null
                return asset('storage/' . $this->group_avatar);
            }

            return null;
        }

        return $this->other_user?->avatar_url;
    }
}
