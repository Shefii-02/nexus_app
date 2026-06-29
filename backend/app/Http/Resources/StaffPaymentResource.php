<?php

namespace App\Http\Resources;

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

            'salary_month' =>
            date('Y-m', strtotime($this->salary_month)),

            'salary_amount' =>
            $this->salary_amount,

            'bonus_amount' =>
            $this->bonus_amount,

            'deduction_amount' =>  $this->deduction_amount,

            'final_amount' =>  $this->final_amount,

            'status' =>  $this->status,

            'payment_method'    => $this->payment_method,
            'payment_reference' => $this->payment_reference,
            'transaction_no'    => $this->transaction_no,
            'payment_date'      => date('Y-m-d', strtotime($this->payment_date)),
            'created_by' => $this->created_by,
            'released_by' => $this->released_by,
        ];
    }
}
