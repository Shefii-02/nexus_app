<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaFile extends Model
{
    //
    protected $fillable = [
        'user_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size'
    ];
}
