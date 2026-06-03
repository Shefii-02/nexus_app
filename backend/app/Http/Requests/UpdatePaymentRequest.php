<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => 'sometimes|numeric|min:0.01',
            'payment_date' => 'sometimes|date_format:Y-m-d',
            'payment_method' => 'sometimes|in:cash,check,bank_transfer,other',
            'reference_number' => 'sometimes|string|max:255',
            'notes' => 'sometimes|string',
            'status' => 'sometimes|in:pending,verified,rejected',
        ];
    }
}
