<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    protected $fillable = [
        'notification_id',
        'user_id',
        'action',
        'payload'
    ];

    protected $casts = [
        'payload' => 'array'
    ];
}
