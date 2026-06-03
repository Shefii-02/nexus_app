<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray($req)
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'date' => $this->payment_date,
            'student' => $this->student->user->name ?? null,
            'course' => $this->course->name ?? null,
            'status' => $this->status,
        ];
    }
}
