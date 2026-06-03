<?php

namespace App\DTOs;

class LeaveRequestDTO
{
    public function __construct(

        public int $user_id,

        public string $user_type,

        public string $from_date,

        public string $to_date,

        public string $leave_type,

        public string $reason,

        public ?string $remarks = null
    ) {}

    public static function fromArray(
        array $data
    ): self {

        return new self(

            $data['user_id'],

            $data['user_type'],

            $data['from_date'],

            $data['to_date'],

            $data['leave_type'],

            $data['reason'],

            $data['remarks'] ?? null
        );
    }

    public function toArray(): array
    {
        return [

            'user_id' =>
                $this->user_id,

            'user_type' =>
                $this->user_type,

            'from_date' =>
                $this->from_date,

            'to_date' =>
                $this->to_date,

            'total_days' =>
                now()
                    ->parse($this->from_date)
                    ->diffInDays(
                        $this->to_date
                    ) + 1,

            'leave_type' =>
                $this->leave_type,

            'reason' =>
                $this->reason,

            'remarks' =>
                $this->remarks
        ];
    }
}
