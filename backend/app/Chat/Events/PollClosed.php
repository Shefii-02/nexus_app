<?php
namespace App\Chat\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class PollClosed implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $conversationId,
        public int $pollId,
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel("conversation.{$this->conversationId}")];
    }

    public function broadcastAs(): string
    {
        return 'poll.closed';
    }

    public function broadcastWith(): array
    {
        return [
            'poll_id'   => $this->pollId,
            'is_closed' => true,
            'closed_at' => now()->toISOString(),
        ];
    }
}
