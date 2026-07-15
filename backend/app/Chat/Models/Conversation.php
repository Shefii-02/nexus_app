<?php

namespace App\Chat\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Conversation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'type', 'title', 'created_by', 'avatar', 'module_id', 'status','reply_permission'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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
        $userRank = self::ROLE_RANK[$user->acc_type] ?? 0;
        $requiredRank = self::ROLE_RANK[$this->reply_permission] ?? 0;

        return $userRank >= $requiredRank;
    }

    // ─── Relationships ────────────────────────────────────────────────────

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(\App\Models\ConversationParticipant::class);
    }

    public function activeParticipants(): HasMany
    {
        return $this->hasMany(\App\Models\ConversationParticipant::class)
            ->where('status', 'active')
            ->whereNull('deleted_at');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\User::class, 'conversation_participants')
            ->withPivot(['last_read_at', 'is_muted', 'is_pinned', 'status', 'left_at'])
            ->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function lastMessage(): HasMany
    {
        return $this->hasMany(Message::class)->latest()->limit(1);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(\App\Models\ConversationReport::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────

    public function scopeForUser($query, int $userId)
    {
        return $query->whereHas('participants', function ($q) use ($userId) {
            $q->where('user_id', $userId)
              ->where('status', 'active')
              ->whereNull('deleted_at');
        });
    }

    public function scopeForModule($query, int $moduleId)
    {
        return $query->where('module_id', $moduleId);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────

    /**
     * For single chats: find existing conversation between two users
     * scoped to a module to avoid duplicates.
     */
    public static function findIndividual(int $userA, int $userB, ?int $moduleId = null): ?self
    {
        return self::where('type', 'single')
            ->when($moduleId, fn($q) => $q->where('module_id', $moduleId))
            ->whereHas('participants', fn($q) => $q->where('user_id', $userA))
            ->whereHas('participants', fn($q) => $q->where('user_id', $userB))
            ->first();
    }

    public function getUnreadCountFor(int $userId): int
    {
        $participant = $this->participants->firstWhere('user_id', $userId);
        if (!$participant) return 0;

        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_deleted', false)
            ->when($participant->last_read_at, fn($q) =>
                $q->where('created_at', '>', $participant->last_read_at)
            )
            ->count();
    }
}
