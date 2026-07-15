<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
