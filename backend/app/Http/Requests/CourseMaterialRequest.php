<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class CourseMaterialRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // // 🔹 RELATION
            // 'course_id' => [
            //     $this->isMethod('post') ? 'required' : 'sometimes',
            //     'exists:courses,id',
            // ],

            // 🔹 BASIC
            'title' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'string',
                'max:255',
            ],

            'description' => 'nullable|string',

            'file_url' => [$this->isMethod('post') ? 'required' : 'sometimes', 'max:2048'],

            // 🔹 TYPE
            'material_type' => [
                'required',
                Rule::in(['video', 'pdf', 'document', 'link', 'image']),
            ],

            // 🔹 ORDERING
            'order' => 'nullable|integer|min:1',

            // 🔹 STATUS
            'status' => [
                'nullable',
                Rule::in(['active', 'inactive']),
            ],
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
