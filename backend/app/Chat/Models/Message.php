<?php

namespace App\Chat\Models;

use App\Models\MediaFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'conversation_id',
        'sender_id',
        'message',
        'type',
        'media_url',
        'media_meta',
        'reply_to',
        'is_deleted',
        'is_edited',
        'is_pinned',
        'deleted_at'
    ];

    protected $casts = [
        'media_meta' => 'array',
        'is_deleted' => 'boolean',
        'is_edited'  => 'boolean',
        'is_pinned'  => 'boolean',
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(
            Conversation::class
        );
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(
            \App\Models\User::class,
            'sender_id'
        );
    }

    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(
            Message::class,
            'reply_to'
        );
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
        return $this->hasMany(
            \App\Models\MessageRead::class
        );
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(
            \App\Models\MessageReaction::class
        );
    }

    public function reports(): HasMany
    {
        return $this->hasMany(
            \App\Models\MessageReport::class
        );
    }

    public function deletedFor(): HasMany
    {
        return $this->hasMany(
            \App\Models\DeletedMessage::class
        );
    }

    public function scopeVisibleTo(
        $query,
        int $userId
    ) {
        return $query->whereDoesntHave(
            'deletedFor',
            fn($q) => $q->where(
                'user_id',
                $userId
            )
        );
    }
}
