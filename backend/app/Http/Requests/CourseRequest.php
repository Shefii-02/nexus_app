<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class CourseRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $course = $this->route('course');
        $courseId = is_object($course) ? $course->id : $course;

        return [

            // 🔹 BASIC
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'code' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'string',
                'max:100',
                Rule::unique('courses', 'code')->ignore($courseId),
            ],

            'name' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'string',
                'max:255',
            ],

            'description' => 'nullable|string',

            // 🔹 DATE
            'started_at' => 'nullable|date',
            'ended_at' => 'nullable|date|after_or_equal:started_at',

            // 🔹 PRICING
            'actual_price' => 'nullable|numeric|min:0',
            'net_price' => 'nullable|numeric|min:0|lte:actual_price',

            // 🔹 FLAGS
            'coupon_available' => 'boolean',
            'is_renewal' => 'boolean',

            // 🔹 TYPE
            'class_type' => [
                'required',
                Rule::in(['online', 'offline', 'hybrid']),
            ],

            // 🔹 RELATION
            'teacher_id' => [
                'nullable',
                'exists:users,id', // ⚠️ change if using users table
            ],

            // 🔹 FEE
            'fee_type' => [
                'required',
                Rule::in(['monthly', 'one_time']),
            ],

            'duration_days' => 'nullable|integer|min:1|max:60',

            // 🔹 STATUS
            'status' => [
                'required',
                Rule::in(['active', 'inactive', 'archived']),
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
