<?php

use App\Models\ConversationParticipant;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
| Register all private/presence channels for the chat module.
*/

// Private channel for conversation messages
Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    return ConversationParticipant::where('conversation_id', $conversationId)
        ->where('user_id', $user->id)
        ->where('status', 'active')
        ->whereNull('deleted_at')
        ->exists();
});

// Presence channel for typing indicators + online presence
Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    $isParticipant = ConversationParticipant::where('conversation_id', $conversationId)
        ->where('user_id', $user->id)
        ->where('status', 'active')
        ->whereNull('deleted_at')
        ->exists();

    if ($isParticipant) {
        return [
            'id'     => $user->id,
            'name'   => $user->name,
            'avatar' => $user->avatar,
        ];
    }
    return false;
});

// Public user-status channel
Broadcast::channel('user-status', function ($user) {
    return true; // all authenticated users can listen
});
