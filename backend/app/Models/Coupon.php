<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use SoftDeletes;

    protected $fillable = [

        'code',

        'title',

        'description',

        'discount_type',

        'discount_value',

        'max_discount_amount',

        'minimum_amount',

        'usage_limit',

        'usage_per_user',

        'start_date',

        'end_date',

        'apply_on',

        'is_active',

        'created_by',
    ];

    public function usages()
    {
        return $this->hasMany(
            CouponUsage::class
        );
    }
}
