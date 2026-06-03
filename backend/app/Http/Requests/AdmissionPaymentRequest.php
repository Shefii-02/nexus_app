<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AdmissionPaymentRequest
extends BaseRequest
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
                'numeric',
                'min:1'
            ],

            'payment_method' => [
                'required',
                'in:cash,upi,card,bank_transfer'
            ],

            'transaction_no' => [
                'nullable',
                'string'
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
