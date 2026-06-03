<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeacherPaymentItemRequest
extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'teacher_id' => [
                'required',
                'exists:users,id'
            ],

            'course_id' => [
                'nullable',
                'exists:courses,id'
            ],

            'month' => [
                'required'
            ],

            'calculation_type' => [
                'required'
            ],

            'student_count' => [
                'nullable',
                'numeric'
            ],

            'course_revenue' => [
                'nullable',
                'numeric'
            ],

            'share_percentage' => [
                'nullable',
                'numeric'
            ],

            'amount' => [
                'required',
                'numeric'
            ],

            'remarks' => [
                'nullable'
            ]
        ];
    }
}
