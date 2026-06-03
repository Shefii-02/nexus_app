<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admission extends Model
{
    use SoftDeletes;

    protected $table = 'admissions';

    protected $fillable = [

        'student_id',
        'course_id',
        'teacher_id',

        'actual_fee',
        'discount_amount',
        'discount_reason',
        'coupon_id',

        'net_fee',

        'admission_date',
        'expiry_date',

        'status',

        'created_by',
    ];

    protected $casts = [

        'actual_fee' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'net_fee' => 'decimal:2',

        'admission_date' => 'date',
        'expiry_date' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function student()
    {
        return $this->belongsTo(
            User::class,
            'student_id'
        );
    }

    public function teacher()
    {
        return $this->belongsTo(
            User::class,
            'teacher_id'
        );
    }

    public function course()
    {
        return $this->belongsTo(
            Course::class,
            'course_id'
        );
    }

    public function coupon()
    {
        return $this->belongsTo(
            Coupon::class,
            'coupon_id'
        );
    }

    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function payments()
    {
        return $this->hasMany(
            AdmissionPayment::class,
            'admission_id'
        );
    }

    public function renewals()
    {
        return $this->hasMany(
            AdmissionRenewal::class,
            'admission_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function getTotalPaidAttribute()
    {
        return $this->payments()
            ->sum('amount');
    }

    public function getBalanceAttribute()
    {
        return $this->net_fee -
            $this->payments()
            ->sum('amount');
    }

    public function getIsExpiredAttribute()
    {
        if (!$this->expiry_date) {
            return false;
        }

        return now()->greaterThan(
            $this->expiry_date
        );
    }

    public function getDaysRemainingAttribute()
    {
        if (!$this->expiry_date) {
            return null;
        }

        return now()->diffInDays(
            $this->expiry_date,
            false
        );
    }


    public function scopeActive($query)
    {
        return $query->where(
            'status',
            'active'
        );
    }

    public function scopeExpired($query)
    {
        return $query->whereDate(
            'expiry_date',
            '<',
            now()
        );
    }

    public function scopeRenewalDue($query)
    {
        return $query->whereDate(
            'expiry_date',
            '<=',
            now()->addDays(7)
        );
    }



}
