<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [

        'user_id',
        'user_type',

        'from_date',
        'to_date',

        'total_days',

        'leave_type',

        'reason',

        'status',

        'approved_by',
        'approved_at',

        'remarks'
    ];

    protected $casts = [

        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(
            User::class
        );
    }

    public function approver()
    {
        return $this->belongsTo(
            User::class,
            'approved_by'
        );
    }
}
