<?php

namespace App\Chat\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// ─── MessageSent ─────────────────────────────────────────────────────────────
class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Message $message) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("conversation.{$this->message->conversation_id}"),
        ];
    }

    public function broadcastAs(): string { return 'message.sent'; }

    public function broadcastWith(): array
    {
        $this->message->load(['sender:id,name,avatar', 'replyTo:id,message,sender_id', 'reactions']);
        return [
            'message' => [
                'id'              => $this->message->id,
                'conversation_id' => $this->message->conversation_id,
                'sender_id'       => $this->message->sender_id,
                'sender'          => $this->message->sender,
                'message'         => $this->message->message,
                'type'            => $this->message->type,
                'media_url'       => $this->message->media_url,
                'media_meta'      => $this->message->media_meta,
                'reply_to'        => $this->message->reply_to,
                'reply_message'   => $this->message->replyTo,
                'is_edited'       => $this->message->is_edited,
                'is_pinned'       => $this->message->is_pinned,
                'reactions'       => $this->message->reactions,
                'created_at'      => $this->message->created_at->toISOString(),
            ],
        ];
    }
}

// ─── MessageUpdated ───────────────────────────────────────────────────────────
class MessageUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Message $message) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel("conversation.{$this->message->conversation_id}")];
    }

    public function broadcastAs(): string { return 'message.updated'; }

    public function broadcastWith(): array
    {
        return ['message' => $this->message->fresh(['reactions'])];
    }
}

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
        return ['message_id' => $this->messageId, 'conversation_id' => $this->conversationId];
    }
}

// ─── MessageRead ──────────────────────────────────────────────────────────────
class MessageReadEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $conversationId,
        public int $userId,
        public string $readAt
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel("conversation.{$this->conversationId}")];
    }

    public function broadcastAs(): string { return 'message.read'; }

    public function broadcastWith(): array
    {
        return [
            'conversation_id' => $this->conversationId,
            'user_id'         => $this->userId,
            'read_at'         => $this->readAt,
        ];
    }
}

// ─── TypingIndicator ─────────────────────────────────────────────────────────
class TypingIndicator implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $conversationId,
        public int $userId,
        public string $userName,
        public bool $isTyping
    ) {}

    public function broadcastOn(): array
    {
        return [new PresenceChannel("conversation.{$this->conversationId}")];
    }

    public function broadcastAs(): string { return 'typing'; }

    public function broadcastWith(): array
    {
        return [
            'user_id'   => $this->userId,
            'user_name' => $this->userName,
            'typing'    => $this->isTyping,
        ];
    }
}

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

    public function broadcastAs(): string { return 'user.status'; }

    public function broadcastWith(): array
    {
        return [
            'user_id'   => $this->userId,
            'online'    => $this->online,
            'last_seen' => $this->lastSeen,
        ];
    }
}

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

    public function broadcastAs(): string { return 'reaction.added'; }

    public function broadcastWith(): array
    {
        return [
            'message_id' => $this->messageId,
            'user_id'    => $this->userId,
            'reaction'   => $this->reaction,
        ];
    }
}
