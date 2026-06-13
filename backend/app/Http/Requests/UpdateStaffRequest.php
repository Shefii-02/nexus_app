<?php

namespace App\Http\Requests;


use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateStaffRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $staff = $this->route('staff');



        return [
            'name' => 'required|string|max:255',

            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($staff),
            ],

            'phone' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users', 'phone')->ignore($staff),
            ],
            'department' => 'sometimes|string|max:255',
            'designation' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20|unique:users,phone,' . $this->route('staff'),
            'address' => 'sometimes|string|max:500',
            'status' => 'sometimes|in:active,inactive,suspended',
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
