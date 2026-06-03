<?php
namespace App\DTOs;

class CouponDTO
{
    public function __construct(

        public string $code,

        public string $title,

        public ?string $description,

        public string $discount_type,

        public float $discount_value,

        public ?float $max_discount_amount,

        public float $minimum_amount,

        public ?int $usage_limit,

        public int $usage_per_user,

        public string $start_date,

        public string $end_date,

        public string $apply_on,

        public int $is_active
    ) {}

    public static function fromArray(
        array $data
    ): self {

        return new self(

            code: $data['code'],

            title: $data['title'],

            description: $data['description'] ?? null,

            discount_type:
                $data['discount_type'],

            discount_value:
                $data['discount_value'],

            max_discount_amount:
                $data['max_discount_amount']
                    ?? null,

            minimum_amount:
                $data['minimum_amount']
                    ?? 0,

            usage_limit:
                $data['usage_limit']
                    ?? null,

            usage_per_user:
                $data['usage_per_user']
                    ?? 1,

            start_date:
                $data['start_date'],

            end_date:
                $data['end_date'],

            apply_on:
                $data['apply_on'],

            is_active:
                $data['is_active']
                    ?? 1,
        );
    }

    public function toArray()
    {
        return [

            'code' => $this->code,

            'title' => $this->title,

            'description' =>
                $this->description,

            'discount_type' =>
                $this->discount_type,

            'discount_value' =>
                $this->discount_value,

            'max_discount_amount' =>
                $this->max_discount_amount,

            'minimum_amount' =>
                $this->minimum_amount,

            'usage_limit' =>
                $this->usage_limit,

            'usage_per_user' =>
                $this->usage_per_user,

            'start_date' =>
                $this->start_date,

            'end_date' =>
                $this->end_date,

            'apply_on' =>
                $this->apply_on,

            'is_active' =>
                $this->is_active,

            'created_by' =>
                auth()->id(),
        ];
    }
}
