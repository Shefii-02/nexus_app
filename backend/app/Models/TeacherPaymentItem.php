<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherPaymentItem extends Model
{
    //
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
            Course::class
        );
    }

    public function payments()
    {
        return $this->belongsToMany(
            TeacherPayment::class,
            'teacher_payment_item_payment'
        );
    }

    public function teacherPayment()
    {
        return $this->belongsTo(TeacherPayment::class);
    }
}
