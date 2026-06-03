<?php

namespace App\Chat\Services;

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use App\Models\MessageRead;
use Illuminate\Support\Facades\DB;

class ChatService
{
    /**
     * Find or create an individual conversation between two users.
     * Scoped by module_id to allow teacher-student chats per course.
     */
    public function findOrCreateIndividual(int $userA, int $userB, ?int $moduleId = null): Conversation
    {
        $existing = Conversation::findIndividual($userA, $userB, $moduleId);
        if ($existing) return $existing;

        return DB::transaction(function () use ($userA, $userB, $moduleId) {
            $conversation = Conversation::create([
                'type'       => 'individual',
                'created_by' => $userA,
                'module_id'  => $moduleId,
            ]);

            foreach ([$userA, $userB] as $uid) {
                ConversationParticipant::create([
                    'conversation_id' => $conversation->id,
                    'user_id'         => $uid,
                    'created_by'      => $userA,
                ]);
            }

            return $conversation;
        });
    }

    /**
     * Get conversations for a user with all needed data for the list view.
     */
    public function getConversationsForUser(int $userId, int $perPage = 30): \Illuminate\Pagination\LengthAwarePaginator
    {
        return Conversation::with([
            'participants.user:id,name,avatar,email',
            'messages' => fn($q) => $q->latest()->limit(1),
            'messages.sender:id,name',
        ])
        ->forUser($userId)
        ->where('status', 'active')
        ->orderByDesc(
            Message::select('created_at')
                ->whereColumn('conversation_id', 'conversations.id')
                ->latest()
                ->limit(1)
        )
        ->paginate($perPage);
    }

    /**
     * Mark all unread messages in a conversation as read for a user.
     */
    public function markConversationRead(int $conversationId, int $userId): void
    {
        ConversationParticipant::where('conversation_id', $conversationId)
            ->where('user_id', $userId)
            ->update(['last_read_at' => now()]);

        $unreadIds = Message::where('conversation_id', $conversationId)
            ->where('sender_id', '!=', $userId)
            ->whereDoesntHave('reads', fn($q) => $q->where('user_id', $userId))
            ->pluck('id');

        if ($unreadIds->isEmpty()) return;

        $now   = now();
        $reads = $unreadIds->map(fn($id) => [
            'message_id' => $id,
            'user_id'    => $userId,
            'read_at'    => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ])->toArray();

        MessageRead::insertOrIgnore($reads);
    }

    /**
     * Get paginated messages for a conversation (cursor-based).
     */
    public function getMessages(int $conversationId, int $userId, ?string $cursor = null)
    {
        return Message::with([
            'sender:id,name,avatar',
            'replyTo:id,message,sender_id,type',
            'replyTo.sender:id,name',
            'reactions.user:id,name',
            'reads:message_id,user_id,read_at',
        ])
        ->where('conversation_id', $conversationId)
        ->visibleTo($userId)
        ->orderByDesc('created_at')
        ->cursorPaginate(40, ['*'], 'cursor', $cursor);
    }

    /**
     * Get unread count across all conversations for a user.
     */
    public function getTotalUnreadCount(int $userId): int
    {
        return ConversationParticipant::where('user_id', $userId)
            ->where('status', 'active')
            ->get()
            ->sum(function ($participant) use ($userId) {
                return Message::where('conversation_id', $participant->conversation_id)
                    ->where('sender_id', '!=', $userId)
                    ->where('is_deleted', false)
                    ->when($participant->last_read_at,
                        fn($q) => $q->where('created_at', '>', $participant->last_read_at)
                    )
                    ->count();
            });
    }
}
