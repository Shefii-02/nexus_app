<?php

namespace App\DTOs;

use Ramsey\Uuid\Type\Integer;

class AnnouncementDTO
{
    public function __construct(
        public ?int $image,
        public string $title,
        public string $content,
        public string $target_type = 'all_users',
        public array $user_ids = [],
        public array $role_ids = [],
        public array $batch_ids = [],
        public ?string $start_date = null,
        public ?string $end_date = null,
        public string $priority = 'medium',
        public string $status = 'active',
        public int $is_pinned = 0,
        public int $position = 1,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['image']) ? (int) $data['image'] : null,
            $data['title'],
            $data['content'],
            $data['target_type'],
            $data['user_ids'] ?? [],
            $data['role_ids'] ?? [],
            $data['batch_ids'] ?? [],
            $data['start_date'] ?? null,
            $data['end_date'] ?? null,
            $data['priority'] ?? 'medium',
            $data['status'] ?? 'active',
            $data['is_pinned'] ?? 0,
            $data['position'] ?? 1,
        );
    }

    public function toArray(): array
    {
        return [
            'image' => $this->image,
            'title' => $this->title,
            'content' => $this->content,
            'target_type' => $this->target_type,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'priority' => $this->priority,
            'status' => $this->status,
            'is_pinned' => $this->is_pinned,
            'created_by' => auth()->id(),
            'position' => $this->position,
        ];
    }
}
