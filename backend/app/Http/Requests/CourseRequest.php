<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class CourseRequest extends BaseRequest
{
    /**
     * Authorize request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation Rules.
     */
    public function rules(): array
    {
        $course = $this->route('course');
        $courseId = is_object($course) ? $course->id : $course;

        return [

            /*
            |--------------------------------------------------------------------------
            | BASIC
            |--------------------------------------------------------------------------
            */

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

            /*
            |--------------------------------------------------------------------------
            | DATE
            |--------------------------------------------------------------------------
            */

            'started_at' => 'nullable|date',

            'ended_at' => [
                'nullable',
                'date',
                'after_or_equal:started_at',
            ],

            /*
            |--------------------------------------------------------------------------
            | PRICE
            |--------------------------------------------------------------------------
            */

            'actual_price' => 'nullable|numeric|min:0',

            'net_price' => 'nullable|numeric|min:0|lte:actual_price',

            /*
            |--------------------------------------------------------------------------
            | FLAGS
            |--------------------------------------------------------------------------
            */

            'coupon_available' => 'nullable|boolean',

            'is_renewal' => 'nullable|boolean',

            /*
            |--------------------------------------------------------------------------
            | CLASS
            |--------------------------------------------------------------------------
            */

            'class_type' => [
                'required',
                Rule::in([
                    'online',
                    'offline',
                    'hybrid',
                ]),
            ],

            /*
            |--------------------------------------------------------------------------
            | RELATIONS
            |--------------------------------------------------------------------------
            */

            'teacher_id' => [
                'required',
                'exists:users,id',
            ],

            /*
            |--------------------------------------------------------------------------
            | FEE
            |--------------------------------------------------------------------------
            */

            'fee_type' => [
                'required',
                Rule::in([
                    'monthly',
                    'one_time',
                ]),
            ],

            'duration_days' => 'nullable|integer|min:1|max:60',

            /*
            |--------------------------------------------------------------------------
            | STATUS
            |--------------------------------------------------------------------------
            */

            'status' => [
                'required',
                Rule::in([
                    'active',
                    'inactive',
                    'archived',
                ]),
            ],

        ];
    }

    /**
     * Custom Validation Messages.
     */
    public function messages(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Thumbnail
            |--------------------------------------------------------------------------
            */

            'thumbnail.image' => 'Thumbnail must be a valid image.',
            'thumbnail.mimes' => 'Thumbnail must be JPG, JPEG, PNG or WEBP.',
            'thumbnail.max' => 'Thumbnail size must not exceed 2 MB.',

            /*
            |--------------------------------------------------------------------------
            | Code
            |--------------------------------------------------------------------------
            */

            'code.required' => 'Course code is required.',
            'code.unique' => 'This course code already exists.',
            'code.max' => 'Course code may not exceed 100 characters.',

            /*
            |--------------------------------------------------------------------------
            | Name
            |--------------------------------------------------------------------------
            */

            'name.required' => 'Course name is required.',
            'name.max' => 'Course name may not exceed 255 characters.',

            /*
            |--------------------------------------------------------------------------
            | Description
            |--------------------------------------------------------------------------
            */

            'description.string' => 'Description must be valid text.',

            /*
            |--------------------------------------------------------------------------
            | Dates
            |--------------------------------------------------------------------------
            */

            'started_at.date' => 'Please provide a valid start date.',

            'ended_at.date' => 'Please provide a valid end date.',

            'ended_at.after_or_equal' => 'End date must be greater than or equal to the start date.',

            /*
            |--------------------------------------------------------------------------
            | Prices
            |--------------------------------------------------------------------------
            */

            'actual_price.numeric' => 'Actual price must be numeric.',
            'actual_price.min' => 'Actual price cannot be negative.',

            'net_price.numeric' => 'Net price must be numeric.',
            'net_price.min' => 'Net price cannot be negative.',
            'net_price.lte' => 'Net price cannot be greater than actual price.',

            /*
            |--------------------------------------------------------------------------
            | Boolean
            |--------------------------------------------------------------------------
            */

            'coupon_available.boolean' => 'Coupon Available must be true or false.',

            'is_renewal.boolean' => 'Renewal value must be true or false.',

            /*
            |--------------------------------------------------------------------------
            | Class Type
            |--------------------------------------------------------------------------
            */

            'class_type.required' => 'Please select class type.',

            'class_type.in' => 'Class type must be Online, Offline or Hybrid.',

            /*
            |--------------------------------------------------------------------------
            | Teacher
            |--------------------------------------------------------------------------
            */

            'teacher_id.required' => 'Please select a teacher.',

            'teacher_id.exists' => 'Selected teacher was not found.',

            /*
            |--------------------------------------------------------------------------
            | Fee
            |--------------------------------------------------------------------------
            */

            'fee_type.required' => 'Please select fee type.',

            'fee_type.in' => 'Fee type must be Monthly or One Time.',

            'duration_days.integer' => 'Duration must be a valid number.',

            'duration_days.min' => 'Duration must be at least 1 day.',

            'duration_days.max' => 'Duration cannot exceed 60 days.',

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            'status.required' => 'Please select status.',

            'status.in' => 'Status must be Active, Inactive or Archived.',

        ];
    }

    /**
     * Friendly Attribute Names.
     */
    public function attributes(): array
    {
        return [

            'thumbnail' => 'thumbnail image',

            'code' => 'course code',

            'name' => 'course name',

            'description' => 'course description',

            'started_at' => 'start date',

            'ended_at' => 'end date',

            'actual_price' => 'actual price',

            'net_price' => 'net price',

            'coupon_available' => 'coupon availability',

            'is_renewal' => 'renewal',

            'class_type' => 'class type',

            'teacher_id' => 'teacher',

            'fee_type' => 'fee type',

            'duration_days' => 'duration',

            'status' => 'status',

        ];
    }

    /**
     * Return custom validation response.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
