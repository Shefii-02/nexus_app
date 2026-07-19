<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAppPermission extends Model
{
    protected $fillable = [
        'user_id',
        'permission_key',
        'granted',
    ];

    protected $casts = [
        'granted' => 'boolean',
    ];

    /** Fixed set of valid permission keys — used for validation and seeding. */
    public const KEYS = [
        'group_manage',
        'individual_chat_manage',
        'course_manage',
        'admission_manage',
        'teacher_manage',
        'staff_manage',
        'student_manage',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
