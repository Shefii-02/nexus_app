<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LeaveRequestResource
extends JsonResource
{
    public function toArray($request)
    {
        return [

            'id' =>
                $this->id,

            'user' => [

                'id' =>
                    $this->user?->id,

                'name' =>
                    $this->user?->name
            ],

            'user_type' =>
                $this->user_type,

            'leave_type' =>
                $this->leave_type,

            'from_date' =>
                $this->from_date,

            'to_date' =>
                $this->to_date,

            'total_days' =>
                $this->total_days,

            'reason' =>
                $this->reason,

            'status' =>
                $this->status,

            'remarks' =>
                $this->remarks,

            'approved_at' =>
                $this->approved_at
        ];
    }
}
