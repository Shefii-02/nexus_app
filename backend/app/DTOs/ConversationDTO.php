<?php
namespace App\DTOs;

class ConversationDTO
{
    public function __construct(
        public string $type,
        public ?string $title,
        public array $participants
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            type: $data['type'],
            title: $data['title'] ?? null,
            participants: $data['participants']
        );
    }
}
