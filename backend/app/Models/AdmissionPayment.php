<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdmissionPayment extends Model
{
    use SoftDeletes;

    protected $fillable = [

        'admission_id',
        'student_id',
        'course_id',

        'amount',

        'payment_method',

        'transaction_no',

        'remarks',

        'paid_at',

        'received_by',
    ];

    protected $casts = [

        'amount' => 'decimal:2',

        'paid_at' => 'datetime',
    ];

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

    public function receiver()
    {
        return $this->belongsTo(
            User::class,
            'received_by'
        );
    }
}
