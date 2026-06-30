<?php
// app/Models/Call.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id', 'teacher_id', 'student_id', 'conversation_id',
        'status', 'type', 'started_at', 'answered_at', 'ended_at',
    ];

    protected $casts = [
        'started_at'  => 'datetime',
        'answered_at' => 'datetime',
        'ended_at'    => 'datetime',
    ];

    /** Calls considered "in progress" and therefore blocking new calls. */
    public const ACTIVE_STATUSES = ['ringing', 'active'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
