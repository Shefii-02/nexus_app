<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherPayment extends Model
{
    //

    public function teacher()
    {
        return $this->belongsTo(
            User::class,
            'teacher_id'
        );
    }

    public function items()
    {
        return $this->belongsToMany(
            TeacherPaymentItem::class,
            'teacher_payment_item_payment'
        );
    }
}
