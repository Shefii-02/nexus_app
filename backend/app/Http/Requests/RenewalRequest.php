<?php
namespace App\Http\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class RenewalRequest extends BaseRequest
{
     public function authorize(): bool
    {
        return true;
    }

     public function rules(): array
    {
        return [
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'amount' => 'required|numeric|min:1',
            'renewal_date' => 'required|date',
            'payment_reference' => 'nullable|string',
            'notes' => 'nullable|string',
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
