<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class NotificationRequest extends BaseRequest
{
    public function rules(): array
    {
        return [

            'target_type' => [
                'required',
                Rule::in([
                    'single',
                    'multiple',
                    'students',
                    'teachers',
                    'staff',
                    'all'
                ])
            ],

            'user_id' => [
                'nullable',
                'exists:users,id'
            ],

            'user_ids' => [
                'nullable',
                'array'
            ],

            'user_ids.*' => [
                'exists:users,id'
            ],

            'title' => [
                'required',
                'string',
                'max:255'
            ],

            'message' => [
                'required',
                'string'
            ],

            'type' => [
                'nullable',
                'string'
            ],

            'priority' => [
                'nullable',
                Rule::in([
                    'low',
                    'normal',
                    'high',
                    'urgent'
                ])
            ],

            'action_url' => [
                'nullable',
                'string'
            ],

            'related_model' => [
                'nullable',
                'string'
            ],

            'related_id' => [
                'nullable',
                'integer'
            ],
        ];
    }
}
