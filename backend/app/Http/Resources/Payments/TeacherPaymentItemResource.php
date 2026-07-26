<?php
namespace App\Http\Resources\Payments;
use Illuminate\Http\Resources\Json\JsonResource;

class TeacherPaymentItemResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'               => $this->id,
            'course_id'        => $this->course_id,
            'course_name'      => $this->whenLoaded('course', fn() => $this->course->name ?? null),
            'month'            => $this->month,
            'calculation_type' => $this->calculation_type, // per_class | percentage | fixed
            'student_count'    => $this->student_count,
            'course_revenue'   => (float) $this->course_revenue,
            'share_percentage' => (float) $this->share_percentage,
            'amount'           => (float) $this->amount,
            'remarks'          => $this->remarks,
            'status'           => $this->status,
        ];
    }
}
