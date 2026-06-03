<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'created_by',
        'type',
        'source',
        'title',
        'message',
        'action_url',
        'icon',
        'related_model',
        'related_id',
        'priority',
        'total_receivers'
    ];

    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function targets()
    {
        return $this->hasMany(
            NotificationTargetUser::class
        );
    }

    public function logs()
    {
        return $this->hasMany(
            NotificationLog::class
        );
    }
}
