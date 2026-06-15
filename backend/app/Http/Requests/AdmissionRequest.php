<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
    use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AdmissionRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'student_id' => [
                'required',
                'exists:users,id',
            ],

            'course_id' => [
                'required',
                'exists:courses,id',
            ],


            // 'actual_fee' => [
            //     'required',
            //     'numeric',
            //     'min:0',
            // ],

            // 'discount_amount' => [
            //     'nullable',
            //     'numeric',
            //     'min:0',
            // ],

            // 'discount_reason' => [
            //     'nullable',
            //     'string',
            //     'max:500',
            // ],

            // 'coupon_id' => [
            //     'nullable',
            //     'exists:coupons,id',
            // ],

            // 'net_fee' => [
            //     'required',
            //     'numeric',
            //     'min:0',
            // ],

            // 'admission_date' => [
            //     'required',
            //     'date',
            // ],

            // 'expiry_date' => [
            //     'nullable',
            //     'date',
            //     'after_or_equal:admission_date',
            // ],

            // 'status' => [
            //     'nullable',
            //     'in:active,inactive,completed,cancelled',
            // ],

            /*
            |--------------------------------------------------------------------------
            | Optional First Payment
            |--------------------------------------------------------------------------
            */

            'paid_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'payment_method' => [
                'nullable',
                'in:cash,upi,card,bank_transfer',
            ],

            'transaction_no' => [
                'nullable',
                'string',
                'max:255',
            ],

            'remarks' => [
                'nullable',
                'string',
            ],
        ];
    }

    public function messages(): array
    {
        return [

            'student_id.required' =>
                'Student is required.',

            'course_id.required' =>
                'Course is required.',

            'actual_fee.required' =>
                'Actual fee is required.',

            'net_fee.required' =>
                'Net fee is required.',

            'admission_date.required' =>
                'Admission date is required.',

            'expiry_date.after_or_equal' =>
                'Expiry date must be after admission date.',
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
