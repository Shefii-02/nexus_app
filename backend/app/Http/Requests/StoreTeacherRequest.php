<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTeacherRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'phone' => 'required|string|max:20|unique:users,phone',
            'qualification' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'experience_years' => 'required|integer|min:0|max:100',
            'address' => 'required|string|max:500',
            'status' => 'nullable|sometimes|required|in:active,inactive',
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
