<?php

namespace App\Services\Message;

use App\DTOs\MessageDTO;
use App\Models\DeletedMessage;
use App\Repositories\MessageRepository;

class MessageService
{
    public function __construct(
        private MessageRepository $repo
    ) {}

    public function send(MessageDTO $dto)
    {
        return $this->repo->create([
            'conversation_id' => $dto->conversation_id,
            'sender_id' => auth()->id(),
            'message' => $dto->message,
            'type' => $dto->type,
            'media_url' => $dto->media_url,
            'reply_to' => $dto->reply_to,
        ]);
    }

    public function delete(int $messageId, bool $forEveryone = false)
    {
        $message = Message::findOrFail($messageId);

        if ($forEveryone) {

            // 🔥 Only sender can delete for everyone
            if ($message->sender_id !== auth()->id()) {
                throw new \Exception('Not allowed');
            }

            $message->update([
                'is_deleted' => true,
                'deleted_at' => now(),
                'message' => null,
                'media_url' => null
            ]);
        } else {

            // 🔹 Delete only for current user
            DeletedMessage::firstOrCreate([
                'message_id' => $messageId,
                'user_id' => auth()->id(),
            ]);
        }
    }
}
