<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdmissionRenewal extends Model
{
    use SoftDeletes;

    protected $fillable = [

        'admission_id',

        'student_id',

        'course_id',

        'current_expiry_date',

        'renewal_from',

        'renewal_to',

        'amount',

        'discount_amount',

        'final_amount',

        'paid_at',

        'status',

        'remarks',

        'created_by',
    ];

    protected $casts = [

        'current_expiry_date' => 'date',

        'renewal_from' => 'date',

        'renewal_to' => 'date',

        'paid_at' => 'datetime',

        'amount' => 'decimal:2',

        'discount_amount' => 'decimal:2',

        'final_amount' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function admission()
    {
        return $this->belongsTo(
            Admission::class
        );
    }

    public function student()
    {
        return $this->belongsTo(
            User::class,
            'student_id'
        );
    }

    public function course()
    {
        return $this->belongsTo(
            Course::class
        );
    }

    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePending(
        $query
    ) {
        return $query->where(
            'status',
            'pending'
        );
    }

    public function scopePaid(
        $query
    ) {
        return $query->where(
            'status',
            'paid'
        );
    }

    public function scopeDue(
        $query
    ) {
        return $query->where(
            'status',
            'pending'
        )
        ->whereDate(
            'renewal_to',
            '<=',
            now()->addDays(7)
        );
    }
}
