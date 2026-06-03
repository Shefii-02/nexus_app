<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationTargetUser extends Model
{
    protected $fillable = [
        'notification_id',
        'receiver_id',
        'fcm_token',
        'fcm_status',
        'fcm_response',
        'delivered_at',
        'read_at',
        'clicked_at',
        'is_muted'
    ];

    protected $casts = [
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
        'clicked_at' => 'datetime',
    ];

    public function notification()
    {
        return $this->belongsTo(
            Notification::class
        );
    }

    public function receiver()
    {
        return $this->belongsTo(
            User::class,
            'receiver_id'
        );
    }
}
