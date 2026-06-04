<?php

namespace App\Chat\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

// ─── UserOnlineStatus ─────────────────────────────────────────────────────────
class UserOnlineStatus implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $userId,
        public bool $online,
        public ?string $lastSeen = null
    ) {}

    public function broadcastOn(): array
    {
        return [new Channel("user-status")];
    }

    public function broadcastAs(): string
    {
        return 'user.status';
    }

    public function broadcastWith(): array
    {

        $data = [
            'userId' => $this->userId,
            'lastSeen' => $this->lastSeen,
        ];

        Log::info('message user online status  Broadcast', $data);

        return [
            'user_id'   => $this->userId,
            'online'    => $this->online,
            'last_seen' => $this->lastSeen,
        ];
    }
}
