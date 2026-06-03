<?php

namespace App\Chat\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConversationParticipant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'conversation_id', 'user_id', 'last_read_at', 'created_by',
        'is_muted', 'is_pinned', 'status', 'left_at'
    ];

    protected $casts = [
        'last_read_at' => 'datetime',
        'left_at'      => 'datetime',
        'is_muted'     => 'boolean',
        'is_pinned'    => 'boolean',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}


// ─────────────────────────────────────────────────────────────────────────────

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageReaction extends Model
{
    protected $fillable = ['message_id', 'user_id', 'reaction'];

    public function message(): BelongsTo { return $this->belongsTo(Message::class); }
    public function user(): BelongsTo    { return $this->belongsTo(User::class); }
}


// ─────────────────────────────────────────────────────────────────────────────

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageRead extends Model
{
    protected $fillable = ['message_id', 'user_id', 'read_at'];
    protected $casts    = ['read_at' => 'datetime'];

    public function message(): BelongsTo { return $this->belongsTo(Message::class); }
    public function user(): BelongsTo    { return $this->belongsTo(User::class); }
}


// ─────────────────────────────────────────────────────────────────────────────

// namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Relations\BelongsTo;

// class MessageReport extends Model
// {
//     protected $fillable = ['message_id', 'user_id', 'reason'];

//     public function message(): BelongsTo { return $this->belongsTo(Message::class); }
//     public function user(): BelongsTo    { return $this->belongsTo(User::class); }
// }


// // ─────────────────────────────────────────────────────────────────────────────

// namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Relations\BelongsTo;

// class DeletedMessage extends Model
// {
//     protected $fillable = ['message_id', 'user_id'];

//     public function message(): BelongsTo { return $this->belongsTo(Message::class); }
//     public function user(): BelongsTo    { return $this->belongsTo(User::class); }
// }


// // ─────────────────────────────────────────────────────────────────────────────

// namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Relations\BelongsTo;

// class ConversationReport extends Model
// {
//     protected $fillable = ['conversation_id', 'user_id', 'reason'];

//     public function conversation(): BelongsTo { return $this->belongsTo(Conversation::class); }
//     public function user(): BelongsTo         { return $this->belongsTo(User::class); }
// }
