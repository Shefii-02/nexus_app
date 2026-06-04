<?php

namespace App\Chat\Controllers;

use App\Http\Controllers\Controller;
use App\Chat\Models\Conversation;
use App\Models\ConversationParticipant;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ConversationController extends Controller
{
    /**
     * List all conversations for the authenticated user.
     * Includes last message, unread count, participant info.
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $conversations = Conversation::with([
                'participants.user:id,name,avatar',
                'messages' => fn($q) => $q->latest()->limit(1),
                'messages.sender:id,name',
            ])
            ->forUser($userId)
            ->where('status', '!=', 'archived')
            ->orderByDesc(function ($query) {
                $query->select('created_at')
                      ->from('messages')
                      ->whereColumn('conversation_id', 'conversations.id')
                      ->latest()
                      ->limit(1);
            })
            ->paginate(30);

        $conversations->getCollection()->transform(function ($conv) use ($userId) {
            $participant = $conv->participants->firstWhere('user_id', $userId);
            $conv->unread_count  = $conv->getUnreadCountFor($userId);
            $conv->is_muted      = $participant?->is_muted ?? false;
            $conv->is_pinned     = $participant?->is_pinned ?? false;
            $conv->last_message  = $conv->messages->first();

            // For individual chats, expose the other user as the "title"
            if ($conv->type === 'individual') {
                $other = $conv->participants->firstWhere('user_id', '!=', $userId);
                $conv->other_user = $other?->user;
            }
            unset($conv->messages);
            return $conv;
        });

        return response()->json($conversations);
    }

    /**
     * Create an individual chat — prevents duplicates per module.
     */
    public function createIndividual(Request $request): JsonResponse
    {
        $request->validate([
            'user_id'   => 'required|exists:users,id',
            'module_id' => 'nullable|integer',
        ]);

        $authId   = $request->user()->id;
        $targetId = $request->user_id;
        $moduleId = $request->module_id;

        if ($authId === $targetId) {
            return response()->json(['message' => 'Cannot chat with yourself.'], 422);
        }

        // Find existing conversation (scoped to module to avoid duplicates)
        $existing = Conversation::findIndividual($authId, $targetId, $moduleId);
        if ($existing) {
            return response()->json(['conversation' => $existing->load('participants.user:id,name,avatar')]);
        }

        DB::beginTransaction();
        try {
            $conversation = Conversation::create([
                'type'       => 'individual',
                'created_by' => $authId,
                'module_id'  => $moduleId,
            ]);

            foreach ([$authId, $targetId] as $uid) {
                ConversationParticipant::create([
                    'conversation_id' => $conversation->id,
                    'user_id'         => $uid,
                    'created_by'      => $authId,
                ]);
            }

            DB::commit();
            return response()->json([
                'conversation' => $conversation->load('participants.user:id,name,avatar')
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create conversation.'], 500);
        }
    }

    /**
     * Create a group conversation.
     */
    public function createGroup(Request $request): JsonResponse
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'user_ids'    => 'required|array|min:2',
            'user_ids.*'  => 'exists:users,id',
            'avatar'      => 'nullable|string',
            'module_id'   => 'nullable|integer',
        ]);

        $authId = $request->user()->id;
        $userIds = array_unique(array_merge([$authId], $request->user_ids));

        DB::beginTransaction();
        try {
            $conversation = Conversation::create([
                'type'       => 'group',
                'title'      => $request->title,
                'created_by' => $authId,
                'avatar'     => $request->avatar,
                'module_id'  => $request->module_id,
            ]);

            foreach ($userIds as $uid) {
                ConversationParticipant::create([
                    'conversation_id' => $conversation->id,
                    'user_id'         => $uid,
                    'created_by'      => $authId,
                ]);
            }

            DB::commit();
            return response()->json([
                'conversation' => $conversation->load('participants.user:id,name,avatar')
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create group.'], 500);
        }
    }

    /**
     * Get a single conversation with participants.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $userId = $request->user()->id;

        $conversation = Conversation::forUser($userId)
            ->with('participants.user:id,name,avatar,email')
            ->findOrFail($id);

        if ($conversation->type === 'individual') {
            $other = $conversation->participants
                ->firstWhere('user_id', '!=', $userId);
            $conversation->other_user = $other?->user;
        }

        return response()->json(['conversation' => $conversation]);
    }

    /**
     * Update group info (title, avatar).
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $conversation = Conversation::where('type', 'group')
            ->where('created_by', $request->user()->id)
            ->findOrFail($id);

        $request->validate([
            'title'  => 'sometimes|string|max:255',
            'avatar' => 'sometimes|nullable|string',
            'status' => 'sometimes|in:active,archived',
        ]);

        $conversation->update($request->only('title', 'avatar', 'status'));

        return response()->json(['conversation' => $conversation]);
    }

    /**
     * Leave or remove a participant.
     */
    public function leaveGroup(Request $request, int $id): JsonResponse
    {
        $userId = $request->user()->id;

        $participant = ConversationParticipant::where('conversation_id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();

        $participant->update(['status' => 'left', 'left_at' => now()]);

        return response()->json(['message' => 'Left group successfully.']);
    }

    /**
     * Add participants to a group.
     */
    public function addParticipants(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'user_ids'   => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $conversation = Conversation::where('type', 'group')
            ->forUser($request->user()->id)
            ->findOrFail($id);

        foreach ($request->user_ids as $uid) {
            ConversationParticipant::updateOrCreate(
                ['conversation_id' => $id, 'user_id' => $uid],
                ['status' => 'active', 'left_at' => null, 'created_by' => $request->user()->id, 'deleted_at' => null]
            );
        }

        return response()->json([
            'conversation' => $conversation->load('participants.user:id,name,avatar')
        ]);
    }

    /**
     * Mute/unmute conversation.
     */
    public function toggleMute(Request $request, int $id): JsonResponse
    {
        $participant = ConversationParticipant::where('conversation_id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $participant->update(['is_muted' => !$participant->is_muted]);

        return response()->json(['is_muted' => $participant->is_muted]);
    }

    /**
     * Pin/unpin conversation.
     */
    public function togglePin(Request $request, int $id): JsonResponse
    {
        $participant = ConversationParticipant::where('conversation_id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $participant->update(['is_pinned' => !$participant->is_pinned]);

        return response()->json(['is_pinned' => $participant->is_pinned]);
    }

    /**
     * Report a conversation.
     */
    public function report(Request $request, int $id): JsonResponse
    {
        $request->validate(['reason' => 'required|string']);

        \App\Models\ConversationReport::create([
            'conversation_id' => $id,
            'user_id'         => $request->user()->id,
            'reason'          => $request->reason,
        ]);

        return response()->json(['message' => 'Reported successfully.']);
    }
}
