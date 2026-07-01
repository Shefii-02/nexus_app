<?php

namespace App\Chat\Controllers;

use App\Chat\Events\MessageReadEvent;
use App\Http\Controllers\Controller;
use App\Chat\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\DeletedMessage;
use App\Chat\Models\Message;
use App\Models\MessageReaction;
use App\Models\MessageRead;
use App\Chat\Events\MessageSent;
use App\Chat\Events\MessageUpdated;
use App\Chat\Events\MessageDeleted;
use App\Chat\Events\MessagePinned;
use App\Chat\Events\ReactionAdded;
use App\Chat\Events\ReactionRemoved;
use App\Chat\Events\UserNewMessage;
use App\Chat\Resources\MessageResource;
use App\Http\Controllers\API\ApiResponse;
use App\Services\Notification\FcmNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class MessageController extends Controller
{
    use ApiResponse;

    /**
     * Paginated messages for a conversation (cursor-based).
     */
    public function index(Request $request, int $conversationId): JsonResponse
    {
        $userId = $request->user()->id;

        abort_unless(
            ConversationParticipant::where('conversation_id', $conversationId)
                ->where('user_id', $userId)
                ->where('status', 'active')
                ->exists(),
            403,
            'Not a participant.'
        );

        $messages = Message::with([
            'media',
            'sender:id,name,avatar',
            'replyTo:id,conversation_id,sender_id,message,type,media_url,is_deleted,created_at',
            'replyTo.sender:id,name',
            'reactions.user:id,name',
            'reads:message_id,user_id,read_at',
        ])
            ->where('conversation_id', $conversationId)
            ->visibleTo($userId)
            ->latest()
            ->cursorPaginate(40);


        $this->markAsRead($conversationId, $userId);
        $messageDatas =   MessageResource::collection($messages)->response();
        return $messageDatas;
    }
    // public function index(Request $request, int $conversationId): JsonResponse
    // {
    //     $userId = $request->user()->id;

    //     // Ensure user is a participant
    //     abort_unless(
    //         ConversationParticipant::where('conversation_id', $conversationId)
    //             ->where('user_id', $userId)->where('status', 'active')->exists(),
    //         403,
    //         'Not a participant.'
    //     );

    //     $messages = Message::with(['sender:id,name,avatar', 'replyTo.sender:id,name', 'reactions.user:id,name', 'reads:message_id,user_id,read_at'])
    //         ->where('conversation_id', $conversationId)
    //         ->visibleTo($userId)
    //         ->orderByDesc('created_at')
    //         ->cursorPaginate(40);

    //     // Mark all as read
    //     $this->markAsRead($conversationId, $userId);

    //     return response()->json(MessageResource::collect($messages));
    // }

    /**
     * Send a message (text or media).
     */
    public function store(
        Request $request,
        int $conversationId
    ): JsonResponse {

        $userId = $request->user()->id;

        abort_unless(
            ConversationParticipant::where(
                'conversation_id',
                $conversationId
            )
                ->where('user_id', $userId)
                ->where('status', 'active')
                ->exists(),
            403
        );

        $request->validate([
            'message'  => 'nullable|string|max:5000',
            'type'     => 'required|in:text,image,video,audio,file,voice',
            'file'     => 'nullable|file|max:51200',
            'reply_to' => 'nullable|exists:messages,id',
            'duration' => 'nullable|integer',
        ]);

        $mediaId = null;
        $mediaMeta = null;

        if ($request->hasFile('file')) {

            $uploadService = app(
                \App\Chat\Services\MediaUploadService::class
            );

            $result = $uploadService->upload(
                $request->file('file'),
                $userId,
                $conversationId,
                $request->type
            );

            $mediaId = $result['media']->id;

            $mediaMeta = $result['meta'];

            if ($request->filled('duration')) {
                $mediaMeta['duration'] =
                    (int)$request->duration;
            }
        }

        $message = Message::create([
            'conversation_id' => $conversationId,
            'sender_id'       => $userId,
            'message'         => $request->message,
            'type'            => $request->type,
            'media_url'       => $mediaId,
            // 'media_meta'      => $mediaMeta,
            'reply_to'        => $request->reply_to,
        ]);

        Conversation::where(
            'id',
            $conversationId
        )->touch();

        broadcast(
            new MessageSent($message)
        )->toOthers();

        // After: broadcast(new MessageSent($message))->toOthers();

        $participants = ConversationParticipant::where('conversation_id', $conversationId)
            ->where('user_id', '!=', $userId)
            ->where('status', 'active')->get();
        // ->pluck('user_id');

        foreach ($participants as $recipient) {
            $recipientId = $recipient->user_id;
            $unread = Message::where('conversation_id', $conversationId)
                ->where('sender_id', '!=', $recipientId)
                ->whereDoesntHave('reads', fn($q) => $q->where('user_id', $recipientId))
                ->count();

            broadcast(new UserNewMessage(
                $recipientId,
                $conversationId,
                $request->user()->name,
                $request->message ?? '📎 Media',
                $unread
            ));

            // New chat message
            // FCM push — only if participant hasn't muted this conversation
            $isMuted = $recipient->is_muted;
            // ConversationParticipant::where('conversation_id', $conversationId)
            // ->where('user_id', $recipientId)
            // ->value('is_muted');

            if (!$isMuted) {
                (new FcmNotificationService())->sendNewMessage($recipientId, [
                    'conversation_id'   => $conversationId,
                    'sender_name'       => $request->user()->name,
                    'preview'           => Str::limit($message->message ?? '📎 Media', 80),
                    'conversation_name' => $conv?->title ?? $request->user()->name,
                ]);
            }
        }

        return response()->json([
            'message' => new MessageResource(
                $message->load([
                    'sender:id,name,avatar',
                    'replyTo.sender:id,name',
                    'media'
                ])
            )
        ], 201);
    }
    /**
     * Edit a message.
     */
    public function update(Request $request, int $conversationId, int $messageId): JsonResponse
    {
        $message = Message::where('conversation_id', $conversationId)
            ->where('sender_id', $request->user()->id)
            ->where('type', 'text')
            ->findOrFail($messageId);

        $request->validate(['message' => 'required|string|max:5000']);

        $message->update(['message' => $request->message, 'is_edited' => true]);

        broadcast(new MessageUpdated($message))->toOthers();

        return response()->json(['message' => $message]);
    }

    /**
     * Delete a message for everyone (sender only) or just for me.
     */
    public function destroy(Request $request, int $conversationId, int $messageId): JsonResponse
    {
        $userId = $request->user()->id;
        $forAll = $request->boolean('for_everyone', false);
        $message = Message::where('conversation_id', $conversationId)->findOrFail($messageId);

        if ($forAll && $message->sender_id === $userId) {
            $message->update(['is_deleted' => true, 'deleted_at' => now()]);
        } else {
            DeletedMessage::firstOrCreate([
                'message_id' => $messageId,
                'user_id'    => $userId,
            ]);
        }

        broadcast(new MessageDeleted($messageId, $conversationId))->toOthers();

        return response()->json(['success' => true]);
    }

    /**
     * Mark conversation as read.
     */
    public function markRead(Request $request, int $conversationId): JsonResponse
    {
        $userId = $request->user()->id;
        $this->markAsRead($conversationId, $userId);

        return response()->json(['status' => 'ok']);
    }

    /**
     * Add or change a reaction.
     */
    public function addReaction(Request $request, int $conversationId, int $messageId): JsonResponse
    {
        $request->validate(['reaction' => 'required|string|max:10']);
        $userId = $request->user()->id;

        MessageReaction::updateOrCreate(
            ['message_id' => $messageId, 'user_id' => $userId],
            ['reaction'   => $request->reaction]
        );



        broadcast(new ReactionAdded($conversationId, $messageId, $userId, $request->reaction))->toOthers();

        return response()->json(['status' => 'ok']);
    }

    /**
     * Remove a reaction.
     */
    // removeReaction in MessageController
    public function removeReaction(Request $request, int $conversationId, int $messageId): JsonResponse
    {
        MessageReaction::where('message_id', $messageId)
            ->where('user_id', $request->user()->id)
            ->delete();

        broadcast(new ReactionRemoved($conversationId, $messageId, $request->user()->id))->toOthers();

        return response()->json(['status' => 'ok']);
    }
    // public function removeReaction(Request $request, int $conversationId, int $messageId): JsonResponse
    // {
    //     MessageReaction::where('message_id', $messageId)
    //         ->where('user_id', $request->user()->id)
    //         ->delete();

    //     return response()->json(['status' => 'ok']);
    // }

    /**
     * Report a message.
     */
    public function report(Request $request, int $conversationId, int $messageId): JsonResponse
    {
        $request->validate(['reason' => 'required|string']);

        \App\Models\MessageReport::create([
            'message_id' => $messageId,
            'user_id'    => $request->user()->id,
            'reason'     => $request->reason,
        ]);

        return response()->json(['message' => 'Reported.']);
    }

    /**
     * Get pinned messages in a conversation.
     */
    public function pinned(int $conversationId): JsonResponse
    {
        $messages = Message::with('sender:id,name,avatar')
            ->where('conversation_id', $conversationId)
            ->where('is_pinned', true)
            ->where('is_deleted', false)
            ->get();
        // broadcast(new MessageUpdated($messages))->toOthers();
        return response()->json(['messages' => $messages]);
    }

    /**
     * Pin/unpin a message (group admin or conversation creator).
     */
    // public function togglePin(Request $request, int $conversationId, int $messageId): JsonResponse
    // {
    //     $message = Message::where('conversation_id', $conversationId)->findOrFail($messageId);
    //     $message->update(['is_pinned' => !$message->is_pinned]);

    //     broadcast(new MessageUpdated($message))->toOthers();

    //     return response()->json(['is_pinned' => $message->is_pinned]);
    // }
    // togglePin in MessageController
    public function togglePin(Request $request, int $conversationId, int $messageId): JsonResponse
    {
        $message = Message::where('conversation_id', $conversationId)
            ->findOrFail($messageId);

        // If already pinned → unpin
        if ($message->is_pinned) {
            $message->update([
                'is_pinned' => false,
            ]);

            broadcast(
                new MessagePinned($conversationId, $messageId, false)
            )->toOthers();

            return response()->json([
                'success' => true,
                'is_pinned' => false,
            ]);
        }

        // Unpin all messages in this conversation
        Message::where('conversation_id', $conversationId)
            ->where('is_pinned', true)
            ->update([
                'is_pinned' => false,
            ]);

        // Pin selected message
        $message->update([
            'is_pinned' => true,
        ]);

        broadcast(
            new MessagePinned($conversationId, $messageId, true)
        )->toOthers();

        return response()->json([
            'success' => true,
            'is_pinned' => true,
        ]);
    }
    // public function togglePin(Request $request, int $conversationId, int $messageId): JsonResponse
    // {
    //     Message::where('conversation_id', $conversationId)
    //         ->update(['is_pinned' => false]);

    //     $message = Message::where('conversation_id', $conversationId)
    //         ->findOrFail($messageId);

    //     $message->update(['is_pinned' => true]);

    //     broadcast(new MessagePinned($conversationId, $messageId, true))->toOthers();

    //     return response()->json(['success' => true, 'is_pinned' => true]);
    // }
    // public function togglePin(Request $request, int $conversationId, int $messageId): JsonResponse
    // {
    //     Message::where('conversation_id', $conversationId)
    //         ->update(['is_pinned' => false]);

    //     $message = Message::where('conversation_id', $conversationId)
    //         ->findOrFail($messageId);

    //     $message->update(['is_pinned' => true]);

    //     broadcast(new MessageUpdated($message))->toOthers();

    //     return response()->json([
    //         'success' => true,
    //         'is_pinned' => true,
    //     ]);
    // }

    // ─── Private ─────────────────────────────────────────────────────────

    private function markAsRead(int $conversationId, int $userId): void
    {
        // Update participant last_read_at
        ConversationParticipant::where('conversation_id', $conversationId)
            ->where('user_id', $userId)
            ->update(['last_read_at' => now()]);

        // Insert message reads for unread messages
        $unread = Message::where('conversation_id', $conversationId)
            ->where('sender_id', '!=', $userId)
            ->whereDoesntHave('reads', fn($q) => $q->where('user_id', $userId))
            ->pluck('id');

        $now = now();
        $reads = $unread->map(fn($id) => [
            'message_id' => $id,
            'user_id'    => $userId,
            'read_at'    => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ])->toArray();

        if (!empty($reads)) {
            MessageRead::insert($reads);
            broadcast(new MessageReadEvent($conversationId, $userId, $now->toISOString()))->toOthers();
        }
    }
}
