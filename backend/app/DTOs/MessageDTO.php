<?php
namespace App\DTOs;

class MessageDTO
{
    public function __construct(
        public int $conversation_id,
        public ?string $message,
        public string $type,
        public ?string $media_url,
        public ?int $reply_to
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            conversation_id: $data['conversation_id'],
            message: $data['message'] ?? null,
            type: $data['type'],
            media_url: $data['media_url'] ?? null,
            reply_to: $data['reply_to'] ?? null,
        );
    }
}
