<?php

namespace App\Chat\Requests;

use Illuminate\Foundation\Http\FormRequest;

// ─── SendMessageRequest ───────────────────────────────────────────────────────
class SendMessageRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'message'  => 'nullable|string|max:5000|required_without:media',
            'type'     => 'required|in:text,image,video,audio,file,voice',
            'media'    => 'nullable|file|max:51200',
            'reply_to' => 'nullable|integer|exists:messages,id',
        ];
    }

    public function messages(): array
    {
        return [
            'message.required_without' => 'A message or file is required.',
        ];
    }
}

// ─── CreateGroupRequest ───────────────────────────────────────────────────────
class CreateGroupRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title'      => 'required|string|max:255',
            'user_ids'   => 'required|array|min:2',
            'user_ids.*' => 'integer|exists:users,id',
            'avatar'     => 'nullable|string|max:500',
            'module_id'  => 'nullable|integer',
        ];
    }
}

// ─── CreateIndividualRequest ──────────────────────────────────────────────────
class CreateIndividualRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'user_id'   => 'required|integer|exists:users,id',
            'module_id' => 'nullable|integer',
        ];
    }
}

// ─── AddReactionRequest ───────────────────────────────────────────────────────
class AddReactionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'reaction' => 'required|string|max:10',
        ];
    }
}
