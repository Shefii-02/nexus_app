<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreStaffRequest extends BaseRequest
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
            'password' => 'nullable',
            'phone' => 'required|string|max:20|unique:users,phone',
            'department' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
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
