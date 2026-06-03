<?php

namespace App\DTOs;

class NotificationDTO
{
    public function __construct(
        public string $title,
        public string $message,
        public string $type = 'general',
        public string $priority = 'normal',
        public ?string $action_url = null,
        public ?string $related_model = null,
        public ?int $related_id = null,
        public ?int $created_by = null,
        public string $target_type = 'single',
        public array $user_ids = [],
        public ?int $user_id = null,
        public string $status = 'sent',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            message: $data['message'],
            type: $data['type'] ?? 'general',
            priority: $data['priority'] ?? 'normal',
            action_url: $data['action_url'] ?? null,
            related_model: $data['related_model'] ?? null,
            related_id: $data['related_id'] ?? null,
            created_by: auth()->id(),
            target_type: $data['target_type'],
            user_ids: $data['user_ids'] ?? [],
            user_id: $data['user_id'] ?? null,
            status : $data['status'] ?? 'sent',
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
