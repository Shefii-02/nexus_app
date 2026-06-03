<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray($request)
    {
        return [

            'id' => $this->id,

            'type' => $this->type,

            'category' => $this->category,

            'amount' => $this->amount,

            'payment_method' =>
                $this->payment_method,

            'transaction_no' =>
                $this->transaction_no,

            'reference_type' =>
                $this->reference_type,

            'reference_id' =>
                $this->reference_id,

            'description' =>
                $this->description,

            'transaction_date' =>
                $this->transaction_date,

            'created_by' =>
                $this->creator?->name,
        ];
    }
}
