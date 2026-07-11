<?php

namespace App\Repositories;

use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationRepository
{
    public function create(array $data)
    {
        return Conversation::create($data);
    }

    public function findWithMessages(int $conversationId, array $filters = []): Conversation
    {
        $userId = auth()->id();

        return Conversation::with([
            'participants.user:id,name,email',
            'messages' => function ($q) use ($filters, $userId) {

                // 🔍 Search inside messages
                if (!empty($filters['search'])) {
                    $q->where('message', 'like', '%' . $filters['search'] . '%');
                }

                // ❌ Hide deleted-for-me
                $q->whereNotIn('id', function ($sub) use ($userId) {
                    $sub->select('message_id')
                        ->from('deleted_messages')
                        ->where('user_id', $userId);
                });

                $q->with([
                    'sender:id,name',
                    'reactions',
                ])
                ->latest()
                ->paginate($filters['per_page'] ?? 20);
            }
        ])->findOrFail($conversationId);
    }

    public function listForUser(int $userId)
    {
        return Conversation::whereHas('participants', fn($q) =>
            $q->where('user_id', $userId)
        )->with('participants.user')
         ->latest()
         ->get();


    }

    public function findIndividual(array $userIds)
    {
        return Conversation::where('type', 'single')
            ->whereHas('participants', fn($q) => $q->whereIn('user_id', $userIds))
            ->has('participants', '=', count($userIds))
            ->first();
    }
}
