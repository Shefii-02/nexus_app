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

// Private channel — matches Flutter's 'private-conversation.{id}'
// Broadcast::channel('private-conversation.{conversationId}', function ($user, $conversationId) {
//     return ConversationParticipant::where('conversation_id', $conversationId)
//         ->where('user_id', $user->id)
//         ->where('status', 'active')
//         ->whereNull('deleted_at')
//         ->exists();
// });

Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    Log::info('Broadcasting auth check', [
        'user_id'         => $user->id,
        'conversation_id' => $conversationId,
    ]);

    return ConversationParticipant::where('conversation_id', $conversationId)
        ->where('user_id', $user->id)
        ->where('status', 'active')
        ->whereNull('deleted_at')
        ->exists();
});
// User status channel
Broadcast::channel('user-status', function ($user) {
    return true;
});
