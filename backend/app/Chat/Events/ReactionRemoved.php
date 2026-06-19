<?php

namespace App\Chat\Events;

use App\Models\MessageReaction;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ReactionRemoved implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $conversationId,
        public int $messageId,
        public int $userId
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel("conversation.{$this->conversationId}")];
    }

    public function broadcastAs(): string { return 'reaction.removed'; }

    public function broadcastWith(): array
    {
        $reactions = \App\Models\MessageReaction::where('message_id', $this->messageId)
            ->with('user:id,name')
            ->get()
            ->groupBy('reaction')
            ->map(fn($g) => $g->map(fn($r) => [
                'user_id' => $r->user_id,
                'user'    => $r->user,
            ]))->toArray();

        return [
            'message_id' => $this->messageId,
            'reactions'  => $reactions,
        ];
    }
}
