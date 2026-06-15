<?php
// ─────────────────────────────────────────────────────────────────────────────
// File: app/Http/Requests/StoreTransactionRequest.php
// ─────────────────────────────────────────────────────────────────────────────
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'type'             => ['required', 'in:income,expense,refund'],
            'category'         => ['nullable', 'string', 'max:100'],
            'amount'           => ['required', 'numeric', 'min:0.01'],
            'payment_method'   => ['required', 'in:cash,bank_transfer,cheque,online,other'],
            'transaction_date' => ['required', 'date'],
            'description'      => ['nullable', 'string', 'max:500'],
            'reference_type'   => ['nullable', 'string', 'max:100'],
            'reference_id'     => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.in'           => 'Type must be income, expense, or refund.',
            'payment_method.in' => 'Invalid payment method.',
            'amount.min'        => 'Amount must be greater than zero.',
        ];
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// File: app/Http/Requests/UpdateTransactionRequest.php
// ─────────────────────────────────────────────────────────────────────────────
class UpdateTransactionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'type'             => ['sometimes', 'required', 'in:income,expense,refund'],
            'category'         => ['nullable', 'string', 'max:100'],
            'amount'           => ['sometimes', 'required', 'numeric', 'min:0.01'],
            'payment_method'   => ['sometimes', 'required', 'in:cash,bank_transfer,cheque,online,other'],
            'transaction_date' => ['sometimes', 'required', 'date'],
            'description'      => ['nullable', 'string', 'max:500'],
            'reference_type'   => ['nullable', 'string', 'max:100'],
            'reference_id'     => ['nullable', 'string', 'max:100'],
        ];
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// File: app/Http/Requests/StoreTeacherPaymentRequest.php
// ─────────────────────────────────────────────────────────────────────────────
class StoreTeacherPaymentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'teacher_id'       => ['required', 'integer', 'exists:users,id'],
            'period_start'     => ['required', 'date'],
            'period_end'       => ['required', 'date', 'after_or_equal:period_start'],
            'total_classes'    => ['nullable', 'integer', 'min:0'],
            'gross_amount'     => ['required', 'numeric', 'min:0'],
            'deduction_amount' => ['nullable', 'numeric', 'min:0'],
            'deduction_reason' => ['nullable', 'string', 'max:255'],
            'amount'           => ['required', 'numeric', 'min:0'],
            'remarks'          => ['nullable', 'string', 'max:500'],
        ];
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// File: app/Http/Requests/UpdateTeacherPaymentRequest.php
// ─────────────────────────────────────────────────────────────────────────────
class UpdateTeacherPaymentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'teacher_id'       => ['sometimes', 'integer', 'exists:users,id'],
            'period_start'     => ['sometimes', 'date'],
            'period_end'       => ['sometimes', 'date'],
            'total_classes'    => ['nullable', 'integer', 'min:0'],
            'gross_amount'     => ['sometimes', 'numeric', 'min:0'],
            'deduction_amount' => ['nullable', 'numeric', 'min:0'],
            'deduction_reason' => ['nullable', 'string', 'max:255'],
            'amount'           => ['sometimes', 'numeric', 'min:0'],
            'remarks'          => ['nullable', 'string', 'max:500'],
        ];
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// File: app/Http/Requests/ReleaseTeacherPaymentRequest.php
// ─────────────────────────────────────────────────────────────────────────────
class ReleaseTeacherPaymentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'payment_method'    => ['required', 'in:cash,bank_transfer,cheque,online,other'],
            'payment_reference' => ['nullable', 'string', 'max:100'],
            'remarks'           => ['nullable', 'string', 'max:500'],
        ];
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// File: app/Http/Requests/StoreStaffPaymentRequest.php
// ─────────────────────────────────────────────────────────────────────────────
class StoreStaffPaymentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'staff_id'         => ['required', 'integer', 'exists:users,id'],
            'salary_month'     => ['required', 'date_format:Y-m'],
            'salary_amount'    => ['required', 'numeric', 'min:0'],
            'bonus_amount'     => ['nullable', 'numeric', 'min:0'],
            'deduction_amount' => ['nullable', 'numeric', 'min:0'],
            'deduction_reason' => ['nullable', 'string', 'max:255'],
            'final_amount'     => ['required', 'numeric', 'min:0'],
            'remarks'          => ['nullable', 'string', 'max:500'],
        ];
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// File: app/Http/Requests/UpdateStaffPaymentRequest.php
// ─────────────────────────────────────────────────────────────────────────────
class UpdateStaffPaymentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'staff_id'         => ['sometimes', 'integer', 'exists:users,id'],
            'salary_month'     => ['sometimes', 'date_format:Y-m'],
            'salary_amount'    => ['sometimes', 'numeric', 'min:0'],
            'bonus_amount'     => ['nullable', 'numeric', 'min:0'],
            'deduction_amount' => ['nullable', 'numeric', 'min:0'],
            'deduction_reason' => ['nullable', 'string', 'max:255'],
            'final_amount'     => ['sometimes', 'numeric', 'min:0'],
            'remarks'          => ['nullable', 'string', 'max:500'],
        ];
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// File: app/Http/Requests/ReleaseStaffPaymentRequest.php
// ─────────────────────────────────────────────────────────────────────────────
class ReleaseStaffPaymentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'payment_method' => ['required', 'in:cash,bank_transfer,cheque,online,other'],
            'transaction_no' => ['nullable', 'string', 'max:100'],
            'remarks'        => ['nullable', 'string', 'max:500'],
        ];
    }
}
