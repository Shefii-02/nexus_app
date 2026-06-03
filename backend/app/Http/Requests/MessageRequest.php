<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class MessageRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'conversation_id' => 'required|exists:conversations,id',
            'message' => 'nullable|string',
            'type' => 'required|in:text,image,video,file',
            'media_url' => 'nullable|url',
            'reply_to' => 'nullable|exists:messages,id',
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
