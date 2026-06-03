<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class AnnouncementRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'title' => 'required|string|max:255',
            'content' => 'required|string',

            'target_type' => 'required|in:all_users, all_staffs,all_students, all_teachers, selected_users, roles, batches, specific',

            'user_ids' => 'nullable|array',
            'role_ids' => 'nullable|array',
            'batch_ids' => 'nullable|array',

            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'position' => 'nullable|integer',
            'is_pin' => 'nullable',
            'priority' => 'nullable|in:low,normal,medium,high',
            'status' => 'required|in:draft,published,archived',
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
