<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    //

    protected $fillable = ['conversation_id', 'sender_id', 'message', 'type', 'media_url', 'reply_to'];


    public function reactions()
    {
        return $this->hasMany(MessageReaction::class);
    }

    public function poll(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Poll::class);
    }

        public function sender(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'sender_id');
    }

    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(\App\Chat\Models\Message::class, 'reply_to');
    }



    public function media(): BelongsTo
    {
        return $this->belongsTo(
            MediaFile::class,
            'media_url'
        );
    }

    public function reads(): HasMany
    {
        return $this->hasMany(\App\Models\MessageRead::class);
    }



    public function reports(): HasMany
    {
        return $this->hasMany(\App\Models\MessageReport::class);
    }

    public function deletedFor(): HasMany
    {
        return $this->hasMany(\App\Models\DeletedMessage::class);
    }

    // public function scopeVisibleTo($query, int $userId)
    // {
    //     return $query->whereDoesntHave('deletedFor', fn($q) => $q->where('user_id', $userId))
    //                  ->where('is_deleted', false);
    // }

    public function scopeVisibleTo($query, int $userId)
    {
        return $query->whereDoesntHave(
            'deletedFor',
            fn($q) => $q->where('user_id', $userId)
        );
    }


}
