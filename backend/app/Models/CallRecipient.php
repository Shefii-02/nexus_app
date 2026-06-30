<?php
// app/Models/CallRecipient.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallRecipient extends Model
{
    protected $fillable = ['call_id', 'student_id', 'status', 'answered_at', 'ended_at'];

    protected $casts = [
        'answered_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public const ACTIVE_STATUSES = ['ringing', 'answered'];

    public function call()
    {
        return $this->belongsTo(Call::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
