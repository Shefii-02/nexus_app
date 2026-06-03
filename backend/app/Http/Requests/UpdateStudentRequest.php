<?php

namespace App\Http\Requests;


use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $student = $this->route('student');

        // Get user_id from student
        $student = \App\Models\Student::find($student);
        $userId = $student?->user_id;
        return [
            // 🔹 USER fields
            'name' => 'required|string|max:255',

            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($userId),
            ],

            'phone' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users', 'phone')->ignore($userId),
            ],

            'password' => 'sometimes|string|min:6',
            'status' => 'sometimes|in:active,inactive',
            'address' => 'sometimes|string|max:500',
            'guardian_name' => 'sometimes|string|max:255',
            'guardian_phone' => 'sometimes|string|max:20',
            'roll_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('students', 'roll_number')->ignore($student->id),
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
