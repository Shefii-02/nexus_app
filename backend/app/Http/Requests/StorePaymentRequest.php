<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => 'required|integer|exists:students,id',
            'course_id' => 'required|integer|exists:courses,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date_format:Y-m-d',
            'payment_method' => 'required|in:cash,check,bank_transfer,other',
            'reference_number' => 'sometimes|string|max:255',
            'notes' => 'sometimes|string',
        ];
    }
}
