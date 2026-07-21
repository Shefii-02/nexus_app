<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    protected $table = 'app_notifications';
    protected $fillable = ['user_id', 'type', 'section_id', 'title', 'body', 'data', 'read_at'];
    protected $casts = [
        'data'    => 'array',
        'read_at' => 'datetime',
    ];
}
