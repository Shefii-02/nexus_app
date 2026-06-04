<?php

namespace App\Chat\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

// ─── MessageDeleted ───────────────────────────────────────────────────────────
class MessageDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $messageId,
        public int $conversationId
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel("conversation.{$this->conversationId}")];
    }

    public function broadcastAs(): string { return 'message.deleted'; }

    public function broadcastWith(): array
    {
         $data = [
            'message' => $this->messageId
        ];
            Log::info('MessageDelete Broadcast', $data);
        return ['message_id' => $this->messageId, 'conversation_id' => $this->conversationId];
    }
}
