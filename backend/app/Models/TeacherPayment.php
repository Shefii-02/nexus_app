<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherPayment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'teacher_id',
        'period_start',
        'period_end',
        'total_classes',
        'gross_amount',
        'deduction_amount',
        'deduction_reason',
        'amount',
        'payment_method',
        'payment_reference',
        'transaction_no',
        'payment_date',
        'remarks',
        'status',
        'created_by',
        'released_by',
        'paid_at',
    ];

    protected $casts = [
        'gross_amount'     => 'float',
        'deduction_amount' => 'float',
        'amount'           => 'float',
        'paid_at'          => 'datetime',
        'payment_date'     => 'date:Y-m-d',
        'period_start'     => 'date:Y-m-d',
        'period_end'       => 'date:Y-m-d',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function releasedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'released_by');
    }

    public function items()
    {
        return $this->hasMany(TeacherPaymentItem::class);
    }
}
