<?php

namespace App\Chat\Events;

use App\Models\MessageReaction;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UserNewMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $recipientUserId,
        public int $conversationId,
        public string $senderName,
        public string $preview,
        public int $unreadCount
    ) {}

    public function broadcastOn(): array
    {
        // Per-user private channel
        return [new PrivateChannel("user.{$this->recipientUserId}")];
    }

    public function broadcastAs(): string { return 'new.message'; }

    public function broadcastWith(): array
    {
        return [
            'conversation_id' => $this->conversationId,
            'sender_name'     => $this->senderName,
            'preview'         => $this->preview,
            'unread_count'    => $this->unreadCount,
        ];
    }
}
