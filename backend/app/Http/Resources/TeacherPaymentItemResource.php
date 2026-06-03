<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TeacherPaymentItemResource
extends JsonResource
{
    public function toArray($request)
    {
        return [

            'id' => $this->id,

            'teacher' => [
                'id' =>
                    $this->teacher?->id,

                'name' =>
                    $this->teacher?->name,
            ],

            'course' => [
                'id' =>
                    $this->course?->id,

                'title' =>
                    $this->course?->title,
            ],

            'month' =>
                $this->month,

            'calculation_type' =>
                $this->calculation_type,

            'student_count' =>
                $this->student_count,

            'course_revenue' =>
                $this->course_revenue,

            'share_percentage' =>
                $this->share_percentage,

            'amount' =>
                $this->amount,

            'status' =>
                $this->status,
        ];
    }
}
