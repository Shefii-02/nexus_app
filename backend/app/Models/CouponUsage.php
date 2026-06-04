<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CouponUsage extends Model
{

protected $fillable = ['coupon_id',	'user_id',	'admission_id',	'renewal_id',	'original_amount',	'discount_amount',	'final_amount'];

}
