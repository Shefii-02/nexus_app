<?php

namespace App\Chat\Controllers;

use App\Http\Controllers\Controller;
use App\Chat\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        $targetUser = User::where('id',$targetId)->first();

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
                'type'       => 'single',
                'title'       => $targetUser->name,
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


    public function sharedMedia(Request $request, int $conversationId)
    {
        // ── Auth guard ────────────────────────────────────────────────────────
        $user = $request->user();

        // Make sure the authenticated user is actually a participant
        $isMember = DB::table('conversation_participants')
            ->where('conversation_id', $conversationId)
            ->where('user_id', $user->id)
            ->exists();

        if (!$isMember) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // ── Filter by type ────────────────────────────────────────────────────
        // ?type=media  → image + video rows
        // ?type=links  → link rows
        // ?type=docs   → file rows
        $type = $request->query('type', 'media');

        $query = DB::table('messages')
            ->where('conversation_id', $conversationId)
            ->where('is_deleted', 0)          // exclude soft-deleted
            ->whereNull('deleted_at')         // double-check hard deletes
            ->orderBy('id', 'desc');

        switch ($type) {
            case 'media':
                $query->whereIn('type', ['image', 'video', 'audio']);
                break;

            case 'links':
                $query->where('type', 'link');
                break;

            case 'docs':
                $query->where('type', 'file');
                break;

            default:
                return response()->json(['message' => 'Invalid type'], 422);
        }

        $rows = $query->get([
            'id',
            'type',
            'message',      // used as fallback text / link url
            'media_url',
            'created_at',
        ]);

        // ── Shape the response to match SharedMediaItem in Flutter ────────────
        $data = $rows->map(function ($row) use ($type) {

            $base = [
                'id'       => $row->id,
                'type'     => $row->type,
                'sent_at'  => $row->created_at,
            ];

            if ($type === 'media') {
                // media_url holds the image / video URL
                // For video, thumb_url could be a separate column if you add one;
                // for now we use media_url for both.
                return array_merge($base, [
                    'media_url' => $row->media_url,
                    'thumb_url' => $row->media_url, // replace with thumb col if available
                ]);
            }

            if ($type === 'links') {
                // Your link messages store the URL in `message` or `media_url`.
                // Adjust the fields below to match how you save links.
                $url    = $row->media_url ?? $row->message;
                $domain = null;

                if ($url) {
                    $parsed = parse_url($url);
                    $domain = $parsed['host'] ?? null;
                }

                return array_merge($base, [
                    'link_title'  => null,          // add a link_title column if needed
                    'link_url'    => $url,
                    'link_domain' => $domain,
                ]);
            }

            if ($type === 'docs') {
                // media_url holds the file download URL.
                // Extract file name + extension from the URL path.
                $url      = $row->media_url ?? '';
                $fileName = basename(parse_url($url, PHP_URL_PATH) ?? '');
                $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION)) ?: null;

                // File size: add a `file_size` column to messages if you want it,
                // otherwise leave null — Flutter handles null gracefully.
                return array_merge($base, [
                    'file_name' => $fileName ?: ($row->message ?: 'Document'),
                    'file_ext'  => $fileExt,
                    'file_size' => null,   // e.g. $row->file_size if column exists
                    'media_url' => $url,
                ]);
            }

            return $base;
        });

        return response()->json(['data' => $data]);
    }


    // public function sharedMedia(Conversation $conversation, Request $request)
    // {
    //     $type = $request->query('type', 'media'); // 'media' | 'links' | 'docs'

    //     $query = $conversation->messages()->whereNull('deleted_at');

    //     if ($type === 'media') {
    //         $query->whereIn('type', ['image', 'video', 'audio']);
    //     } elseif ($type === 'links') {
    //         $query->where('type', 'link');
    //     } elseif ($type === 'docs') {
    //         $query->where('type', 'file');
    //     }

    //     $items = $query->latest()->get()->map(fn($m) => [
    //         'id'          => $m->id,
    //         'type'        => $m->type,
    //         'media_url'   => $m->file_url,
    //         'thumb_url'   => $m->thumb_url,
    //         'link_title'  => $m->link_title,
    //         'link_url'    => $m->link_url,
    //         'link_domain' => $m->link_domain,
    //         'file_name'   => $m->file_name,
    //         'file_ext'    => $m->file_ext,
    //         'file_size'   => $m->file_size,
    //         'sent_at'     => $m->created_at?->toISOString(),
    //     ]);

    //     return response()->json(['data' => $items]);
    // }
}
