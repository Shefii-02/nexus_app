<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class RenewalResource extends JsonResource
{
    public function toArray($req)
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'date' => $this->renewal_date,
            'student' => $this->student->user->name ?? null,
            'course' => $this->course->name ?? null,
            'status' => $this->status,
        ];
    }
}
