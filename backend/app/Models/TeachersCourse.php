<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeachersCourse extends Model
{
    //
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function teacher()
    {
        return $this->belongsTo(
            User::class,
            'teacher_id',
            'id'
        );
    }
}
