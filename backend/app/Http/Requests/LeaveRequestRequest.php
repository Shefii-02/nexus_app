<?php

namespace App\Http\Requests;


class LeaveRequestRequest
extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'user_id' =>
                ['required','exists:users,id'],

            'user_type' =>
                [
                    'required',
                    'in:teacher,staff'
                ],

            'from_date' =>
                ['required','date'],

            'to_date' =>
                [
                    'required',
                    'date',
                    'after_or_equal:from_date'
                ],

            'leave_type' =>
                ['required'],

            'reason' =>
                ['required']
        ];
    }
}
