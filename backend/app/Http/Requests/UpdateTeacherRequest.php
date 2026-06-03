<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateTeacherRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $teacher = $this->route('teacher');

        // Get user_id from teacher
        $teacher = \App\Models\Teacher::find($teacher);
        $userId = $teacher?->user_id;
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

            'qualification' => 'sometimes|string|max:255',
            'subject' => 'sometimes|string|max:255',
            'experience_years' => 'sometimes|integer|min:0|max:100',
            'address' => 'nullable|sometimes|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'phone.unique' => 'This phone number is already in use.',
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
