<?php

namespace App\Chat\Events;

use App\Models\MessageReaction;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

// ─── ReactionAdded ────────────────────────────────────────────────────────────
class ReactionAdded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $conversationId,
        public int $messageId,
        public int $userId,
        public string $reaction
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel("conversation.{$this->conversationId}")];
    }

    public function broadcastAs(): string
    {
        return 'reaction.added';
    }

    public function broadcastWith(): array
    {

        $reactions = MessageReaction::where('message_id', $this->messageId)
            ->with('user:id,name')
            ->get()
            ->groupBy('reaction')
            ->map(fn($group) => $group->map(fn($r) => [
                'user_id' => $r->user_id,
                'user'    => $r->user,
            ]))->toArray();
        // Log::info('message reactinh  Broadcast',  $reactions);
        return [
            'message_id' => $this->messageId,
            'reactions'  => $reactions,
        ];

        // $data = [
        //     'message' => $this->reaction
        // ];


        //

        // return [
        //     'message_id' => $this->messageId,
        //     'user_id'    => $this->userId,
        //     'reaction'   => $this->reaction,
        // ];
    }
}
