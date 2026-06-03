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

            'month' => [
                'required'
            ],

            'salary_amount' => [
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

            'final_amount' => [
                'required',
                'numeric'
            ],

            'remarks' => [
                'nullable'
            ]
        ];
    }
}
