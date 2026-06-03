<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffPayment extends Model
{
    use SoftDeletes;

    protected $fillable = [

        'staff_id',

        'month',

        'salary_amount',

        'bonus_amount',

        'deduction_amount',

        'final_amount',

        'status',

        'payment_method',

        'transaction_no',

        'payment_date',

        'remarks',

        'released_by',
    ];

    protected $casts = [

        'salary_amount' => 'decimal:2',

        'bonus_amount' => 'decimal:2',

        'deduction_amount' => 'decimal:2',

        'final_amount' => 'decimal:2',
    ];

    public function staff()
    {
        return $this->belongsTo(
            User::class,
            'staff_id'
        );
    }

    public function releaser()
    {
        return $this->belongsTo(
            User::class,
            'released_by'
        );
    }
}
