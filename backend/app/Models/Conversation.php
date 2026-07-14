<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Conversation extends Model
{
    protected $fillable = [
        'type',
        'title',
        'avatar',
        'module_id',
        'status',
        'created_by',
        'reply_permission',
    ];

    public const ROLE_RANK = [
        'admin'   => 3,
        'staff'   => 2,
        'teacher' => 1,
        'student' => 0,
    ];
    /**
     * Can this user send a message (text/image/voice/etc — anything
     * that isn't a reaction) in this conversation?
     */
    public function canUserSend(\App\Models\User $user): bool
    {
        $userRank = self::ROLE_RANK[$user->role] ?? 0;
        $requiredRank = self::ROLE_RANK[$this->reply_permission] ?? 0;

        return $userRank >= $requiredRank;
    }
    // public function participants(): BelongsToMany
    // {
    //     return $this->belongsToMany(User::class, 'conversation_participants', 'conversation_id', 'user_id')
    //         ->withTimestamps();
    // }
    public function participants()
    {
        return $this->hasMany(ConversationParticipant::class);
    }
    public function mParticipants()
    {
        return $this->belongsToMany(
            User::class,
            'conversation_participants',
            'conversation_id',
            'user_id'
        )
            ->withPivot([
                'status',
                'is_muted',
                'is_pinned',
                'left_at',
                'created_by'
            ]);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->whereHas('participants', function ($q) use ($userId) {
            $q->where('user_id', $userId)
                ->where('status', 'active')
                ->whereNull('deleted_at');
        });
    }

    /**
     * Scope a query to only include conversations belonging to a module.
     */
    public function scopeForModule(Builder $query, int $moduleId): Builder
    {
        return $query->where('module_id', $moduleId);
    }


    public function avatarMedia()
    {
        return $this->belongsTo(MediaFile::class, 'avatar');
    }

    public function getAvatarUrlAttribute()
    {
        return $this->avatarMedia  ? asset('storage/' . $this->avatarMedia->file_path) : 'https://ui-avatars.com/api/?name=' . urlencode($this->title);
    }
}
