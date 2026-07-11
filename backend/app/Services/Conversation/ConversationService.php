<?php

namespace App\Services\Conversation;

use App\DTOs\ConversationDTO;
use App\Models\Conversation;
use App\Repositories\ConversationRepository;
use App\Models\ConversationParticipant;
use Illuminate\Support\Facades\DB;

class ConversationService
{
    public function __construct(
        private ConversationRepository $repo
    ) {}

    public function create(ConversationDTO $dto)
    {
        return DB::transaction(function () use ($dto) {

            $authId = auth()->id();
            $participants = array_unique(array_merge($dto->participants, [$authId]));

            // 🔥 Prevent duplicate single chat
            if ($dto->type === 'single' && count($participants) === 2) {
                $existing = $this->repo->findIndividual($participants);
                if ($existing) return $existing;
            }

            $conversation = $this->repo->create([
                'type' => $dto->type,
                'title' => $dto->title,
                'created_by' => $authId,
            ]);

            foreach ($participants as $userId) {
                ConversationParticipant::create([
                    'conversation_id' => $conversation->id,
                    'user_id' => $userId,
                ]);
            }

            return $conversation->load('participants.user');
        });
    }

    public function list()
    {
        return $this->repo->listForUser(auth()->id());
    }

    public function show(int $id)
    {
        // 🔥 mark as read
        ConversationParticipant::where([
            'conversation_id' => $id,
            'user_id' => auth()->id(),
        ])->update(['last_read_at' => now()]);

        return $this->repo->findWithMessages($id);
    }

    public function delete(int $conversationId)
    {
        $userId = auth()->id();

        $participant = ConversationParticipant::where([
            'conversation_id' => $conversationId,
            'user_id' => $userId
        ])->first();

        if (!$participant) {
            throw new \Exception('Not part of this conversation');
        }

        // 🔥 If group → leave group
        $conversation = Conversation::findOrFail($conversationId);

        if ($conversation->type === 'group') {

            $participant->delete(); // leave group

        } else {

            // 🔥 single → soft delete for user
            $participant->delete();
        }
    }
}
