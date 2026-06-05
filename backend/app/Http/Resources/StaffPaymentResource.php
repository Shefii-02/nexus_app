<?php

namespace App\Http\Resources\Payments;

use Illuminate\Http\Resources\Json\JsonResource;

class StaffPaymentResource
extends JsonResource
{
    public function toArray($request)
    {
        return [

            'id' => $this->id,

            'staff' => [
                'id' =>
                    $this->staff?->id,

                'name' =>
                    $this->staff?->name,
            ],

            'month' =>
                $this->month,

            'salary_amount' =>
                $this->salary_amount,

            'bonus_amount' =>
                $this->bonus_amount,

            'deduction_amount' =>
                $this->deduction_amount,

            'final_amount' =>
                $this->final_amount,

            'status' =>
                $this->status,

            'payment_date' =>
                $this->payment_date,
        ];
    }
}
