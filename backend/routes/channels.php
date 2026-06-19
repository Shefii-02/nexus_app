<?php

use App\Models\ConversationParticipant;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
| Register all private/presence channels for the chat module.
*/


Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    Log::info('Channel auth', [
        'user_id'         => $user->id,
        'conversation_id' => $conversationId,
    ]);

    $isMember = ConversationParticipant::where('conversation_id', $conversationId)
        ->where('user_id', $user->id)
        ->where('status', 'active')
        ->whereNull('deleted_at')
        ->exists();

    if (!$isMember) return false;

    // Returning an array satisfies BOTH private and presence auth.
    // Private channels ignore the extra fields; presence channels use them.
    return [
        'id'   => $user->id,
        'name' => $user->name,
    ];
});

// ── user-status (public — no auth needed) ────────────────────────────────────
Broadcast::channel('user-status', function ($user) {
    return true;
});

Broadcast::channel('online-users', function ($user) {
    return [
        'id'   => $user->id,
        'name' => $user->name,
    ];
});


// ── private-user.{id} ────────────────────────────────────────────────────────
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Private channel — matches Flutter's 'private-conversation.{id}'
// Broadcast::channel('private-conversation.{conversationId}', function ($user, $conversationId) {
//     return ConversationParticipant::where('conversation_id', $conversationId)
//         ->where('user_id', $user->id)
//         ->where('status', 'active')
//         ->whereNull('deleted_at')
//         ->exists();
// });

// Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
//     Log::info('Broadcasting auth check', [
//         'user_id'         => $user->id,
//         'conversation_id' => $conversationId,
//     ]);

//     return ConversationParticipant::where('conversation_id', $conversationId)
//         ->where('user_id', $user->id)
//         ->where('status', 'active')
//         ->whereNull('deleted_at')
//         ->exists();
// });
// // User status channel
// Broadcast::channel('user-status', function ($user) {
//     return true;
// });


// Broadcast::channel('user.{userId}', function ($user, $userId) {
//     return (int) $user->id === (int) $userId;
// });
