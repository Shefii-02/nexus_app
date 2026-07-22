<?php

namespace App\Chat\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PollVoteCast implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public int $conversationId,
        public int $pollId,
        public array $tally,
    ) {}

    /**
     * Private conversation channel
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(
                "conversation.{$this->conversationId}"
            ),
        ];
    }

    /**
     * Event name received by Flutter
     *
     * Flutter:
     * case 'poll.voted':
     */
    public function broadcastAs(): string
    {
        return 'poll.voted';
    }

    /**
     * Payload sent through Reverb
     */
    public function broadcastWith(): array
    {
        return [
            'poll_id' => $this->pollId,
            'tally'   => $this->tally,
        ];
    }
}
