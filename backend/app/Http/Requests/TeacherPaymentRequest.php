<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeacherPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'teacher_id'        => ['required', 'integer', 'exists:users,id'],
            'period_start'      => ['required', 'date'],
            'period_end'        => ['required', 'date', 'after_or_equal:period_start'],
            'total_classes'     => ['nullable', 'integer', 'min:0'],
            'gross_amount'      => ['required', 'numeric', 'min:0'],
            'deduction_amount'  => ['nullable', 'numeric', 'min:0'],
            'deduction_reason'  => ['nullable', 'string', 'max:500'],
            'transfer_amount'   => ['required', 'numeric', 'min:0'],
            'payment_method'    => ['nullable', 'string', 'in:bank_transfer,upi,cash,cheque'],
            'payment_reference' => ['nullable', 'string', 'max:255'],
            'transaction_no'    => ['nullable', 'string', 'max:255'],
            'payment_date'      => ['nullable', 'date'],
            'remarks'           => ['nullable', 'string'],
            'status'            => ['nullable', 'in:pending,released'],
        ];
    }
}
