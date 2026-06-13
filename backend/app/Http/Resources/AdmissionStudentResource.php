<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdmissionStudentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'admission_id' => $this->id,

            'student_id' => $this->student_id,

            'student_name' =>
                $this->student?->name,

            'phone' =>
                $this->student?->phone,

            'course_id' =>
                $this->course_id,

            'course_name' =>
                $this->course?->name,

            'admission_date' =>
                $this->admission_date,

            'expiry_date' =>
                $this->expiry_date,

            'status' =>
                $this->status,
        ];
    }
}
