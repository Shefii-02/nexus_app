<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class CouponRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $couponId = $this->route('id');

        return [

            'code' => [
                'required',
                'string',
                'max:50',
                'unique:coupons,code,' . $couponId
            ],

            'title' => [
                'required',
                'string',
                'max:255'
            ],

            'description' => [
                'nullable'
            ],

            'discount_type' => [
                'required',
                'in:fixed,percentage'
            ],

            'discount_value' => [
                'required',
                'numeric',
                'min:0'
            ],

            'max_discount_amount' => [
                'nullable',
                'numeric'
            ],

            'minimum_amount' => [
                'nullable',
                'numeric'
            ],

            'usage_limit' => [
                'nullable',
                'integer'
            ],

            'usage_per_user' => [
                'nullable',
                'integer'
            ],

            'start_date' => [
                'required',
                'date'
            ],

            'end_date' => [
                'required',
                'date'
            ],

            'apply_on' => [
                'required',
                'in:admission,renewal,both'
            ],

            'is_active' => [
                'nullable',
                'boolean'
            ]
        ];
    }
}
