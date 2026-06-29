<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeacherPaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'teacher_id'        => $this->teacher_id,
            'teacher'           => $this->whenLoaded('teacher', fn() => [
                'id'    => $this->teacher->id,
                'name'  => $this->teacher->name,
                'email' => $this->teacher->email,
            ]),
            'period_start'      => date('Y-m-d', strtotime($this->period_start)),
            'period_end'        => date('Y-m-d', strtotime($this->period_end)),
            'total_classes'     => $this->total_classes,
            'gross_amount'      => $this->gross_amount,
            'deduction_amount'  => $this->deduction_amount,
            'deduction_reason'  => $this->deduction_reason,
            'amount'            => $this->amount,
            'payment_method'    => $this->payment_method,
            'payment_reference' => $this->payment_reference,
            'transaction_no'    => $this->transaction_no,
            'payment_date'      => $this->payment_date,
            'remarks'           => $this->remarks,
            'status'            => $this->status,
            'paid_at'           => $this->paid_at,
            'created_by'        => $this->created_by,
            'released_by'       => $this->released_by,
            'created_at'        => $this->created_at?->toDateTimeString(),
            'updated_at'        => $this->updated_at?->toDateTimeString(),
        ];
    }
}
