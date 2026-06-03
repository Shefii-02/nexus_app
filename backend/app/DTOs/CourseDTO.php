<?php

namespace App\DTOs;

class CourseDTO
{
    public function __construct(
        public ?int $thumbnail,
        public string $code,
        public string $name,
        public ?string $description,

        public ?string $started_at,
        public ?string $ended_at,

        public float $actual_price,
        public float $net_price,

        public bool $coupon_available,
        public bool $is_renewal,

        public ?string $class_type,

        public ?int $teacher_id,

        public string $fee_type,
        public int $duration_days,

        public string $status,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            thumbnail: isset($data['thumbnail']) ? (int) $data['thumbnail'] : null,
            // thumbnail: $data['thumbnail'] ?? null,
            code: $data['code'],
            name: $data['name'],
            description: $data['description'] ?? null,

            started_at: $data['started_at'] ?? null,
            ended_at: $data['ended_at'] ?? null,

            actual_price: (float) ($data['actual_price'] ?? 0),
            net_price: (float) ($data['net_price'] ?? 0),

            coupon_available: (bool) ($data['coupon_available'] ?? false),
            is_renewal: (bool) ($data['is_renewal'] ?? false),

            class_type: $data['class_type'] ?? null,

            teacher_id: $data['teacher_id'] ?? null,

            fee_type: $data['fee_type'] ?? 'one_time',
            duration_days: (int) ($data['duration_days'] ?? 1),

            status: $data['status'] ?? 'active',
        );
    }

    public function toArray(): array
    {
        return [
            'thumbnail' => $this->thumbnail,
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,

            'started_at' => $this->started_at,
            'ended_at' => $this->ended_at,

            'actual_price' => $this->actual_price,
            'net_price' => $this->net_price,

            'coupon_available' => $this->coupon_available,
            'is_renewal' => $this->is_renewal,

            'class_type' => $this->class_type,

            'teacher_id' => $this->teacher_id,

            'fee_type' => $this->fee_type,
            'duration_days' => $this->duration_days,

            'status' => $this->status,
        ];
    }
}
