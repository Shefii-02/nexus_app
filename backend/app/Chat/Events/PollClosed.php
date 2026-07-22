<?php

namespace App\Chat\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PollClosed implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public int $conversationId,
        public int $pollId,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(
                "conversation.{$this->conversationId}"
            ),
        ];
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
            'closed_at' => now()->toIso8601String(),
        ];
    }
}
