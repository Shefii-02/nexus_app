<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AdmissionRenewalRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'admission_id' => [
                'required',
                'exists:admissions,id'
            ],

            'amount' => [
                'required',
                'numeric'
            ],

            'discount_amount' => [
                'nullable',
                'numeric'
            ],

            'final_amount' => [
                'required',
                'numeric'
            ],

            'remarks' => [
                'nullable',
                'string'
            ]
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
