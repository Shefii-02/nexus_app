<?php

namespace App\Chat\Events;

use App\Chat\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

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

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        $data = [
            'message' => $this->message->message
        ];

        Log::info('message sending  Broadcast',  $data);

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
