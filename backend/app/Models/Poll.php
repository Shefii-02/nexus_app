<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poll extends Model
{
    protected $fillable = [
        'message_id', 'conversation_id', 'created_by',
        'question', 'allow_multiple_votes', 'is_closed', 'closes_at',
    ];

    protected $casts = [
        'allow_multiple_votes' => 'boolean',
        'is_closed'            => 'boolean',
        'closes_at'            => 'datetime',
    ];

    public function message(): BelongsTo   { return $this->belongsTo(Message::class); }
    public function options(): HasMany     { return $this->hasMany(PollOption::class)->orderBy('sort_order'); }
    public function votes(): HasMany       { return $this->hasMany(PollVote::class); }

    public function totalVoters(): int
    {
        return $this->votes()->distinct('user_id')->count('user_id');
    }
}
