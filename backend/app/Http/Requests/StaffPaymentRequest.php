<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StaffPaymentRequest
extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'staff_id' => [
                'required',
                'exists:users,id'
            ],

            'payment_month' => [
                'required'
            ],

            'gross_amount' => [
                'required',
                'numeric'
            ],

            'bonus_amount' => [
                'nullable',
                'numeric'
            ],

            'deduction_amount' => [
                'nullable',
                'numeric'
            ],

            'transfer_amount' => [
                'required',
                'numeric'
            ],

            'remarks' => [
                'nullable'
            ],

            'deduction_reason' => ['nullable'],
            'payment_method' => ['nullable'],
            'payment_reference' => ['nullable'],
            'transaction_no' => ['nullable'],
            'payment_date' => ['nullable'],

            'status' => ['nullable'],
        ];
    }
}
