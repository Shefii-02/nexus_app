<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    protected $fillable = [

        'type',

        'category',

        'reference_type',

        'reference_id',

        'amount',

        'payment_method',

        'transaction_no',

        'transaction_date',

        'description',

        'created_by',
    ];

    protected $casts = [

        'amount' => 'decimal:2',

        'transaction_date' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }
}
