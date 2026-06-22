<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class CourseClassRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // 🔹 RELATIONS
            // 'course_id' => [
            //     $this->isMethod('post') ? 'required' : 'sometimes',
            //     'exists:courses,id',
            // ],

            'teacher_id' => [
                'nullable',
                'exists:users,id',
            ],

            // 🔹 BASIC
            'title' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'string',
                'max:255',
            ],

            'description' => 'nullable|string',

            // 🔹 LINKS
            'class_link' => 'nullable|url|max:500',
            'record_link' => 'nullable|url|max:500',

            // 🔹 SOURCE
            'source' => [
                'nullable',
                Rule::in(['youtube', 'zoom', 'google_meet', 'offline', 'other']),
            ],

            // 🔹 CLASS INFO
            'class_number' => 'nullable',

            'scheduled_date' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'date',
            ],

            'ended_at' => 'required',

            'started_at' => 'required',

            'duration_minutes' => 'nullable',

            'room_location' => 'nullable|string|max:255',

            // 🔹 STATUS
            'status' => [
                'nullable',
                Rule::in(['scheduled', 'completed', 'cancelled','draft']),
            ],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422));
    }
}


