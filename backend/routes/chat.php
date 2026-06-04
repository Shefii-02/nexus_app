<?php

use App\Chat\Controllers\ConversationController;
use App\Chat\Controllers\MessageController;
use App\Chat\Events\TypingIndicator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Chat Module Routes (JWT protected via api middleware)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:api'])->prefix('chat')->group(function () {

    // ─── Conversations ───────────────────────────────────────────────────
    Route::get('conversations',                  [ConversationController::class, 'index']);
    Route::post('conversations/individual',      [ConversationController::class, 'createIndividual']);
    Route::post('conversations/group',           [ConversationController::class, 'createGroup']);
    Route::get('conversations/{id}',             [ConversationController::class, 'show']);
    Route::put('conversations/{id}',             [ConversationController::class, 'update']);
    Route::delete('conversations/{id}/leave',    [ConversationController::class, 'leaveGroup']);
    Route::post('conversations/{id}/participants',[ConversationController::class, 'addParticipants']);
    Route::post('conversations/{id}/mute',       [ConversationController::class, 'toggleMute']);
    Route::post('conversations/{id}/pin',        [ConversationController::class, 'togglePin']);
    Route::post('conversations/{id}/report',     [ConversationController::class, 'report']);
     Route::get('conversations/{conversation}/shared-media', [ConversationController::class, 'sharedMedia']);
    // ─── Messages ────────────────────────────────────────────────────────
    Route::get('conversations/{conversationId}/messages',             [MessageController::class, 'index']);
    Route::post('conversations/{conversationId}/messages',            [MessageController::class, 'store']);
    Route::put('conversations/{conversationId}/messages/{messageId}', [MessageController::class, 'update']);
    Route::delete('conversations/{conversationId}/messages/{messageId}', [MessageController::class, 'destroy']);
    Route::post('conversations/{conversationId}/messages/read',       [MessageController::class, 'markRead']);
    Route::get('conversations/{conversationId}/messages/pinned',      [MessageController::class, 'pinned']);
    Route::post('conversations/{conversationId}/messages/{messageId}/pin',     [MessageController::class, 'togglePin']);
    Route::post('conversations/{conversationId}/messages/{messageId}/react',   [MessageController::class, 'addReaction']);
    Route::delete('conversations/{conversationId}/messages/{messageId}/react', [MessageController::class, 'removeReaction']);
    Route::post('conversations/{conversationId}/messages/{messageId}/report',  [MessageController::class, 'report']);

    // ─── Typing Indicator ────────────────────────────────────────────────
    Route::post('conversations/{conversationId}/typing', function (Request $request, int $conversationId) {
        $user = $request->user();
        broadcast(new TypingIndicator(
            $conversationId,
            $user->id,
            $user->name,
            $request->boolean('typing')
        ))->toOthers();
        return response()->json(['status' => 'ok']);
    });
});

// ─── Broadcasting Auth (Reverb) ───────────────────────────────────────────────
// In broadcasting.php channels file:
/*
Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    return \App\Models\ConversationParticipant::where('conversation_id', $conversationId)
        ->where('user_id', $user->id)
        ->where('status', 'active')
        ->exists();
});
*/
