<?php

namespace App\Chat\Controllers;

use App\Http\Controllers\Controller;
use App\Chat\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\User;
use App\Services\Notification\FcmNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ConversationController extends Controller
{
    /**
     * List all conversations for the authenticated user.
     * Includes last message, unread count, participant info.
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $user   = $request->user();

        $conversations = Conversation::with([
            'participants.user:id,name,avatar,acc_type',
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

        $conversations->getCollection()->transform(function ($conv) use ($user) {
            $participant = $conv->participants->firstWhere('user_id', $user->id);
            $conv->unread_count  = $conv->getUnreadCountFor($user->id);
            $conv->is_muted      = $participant?->is_muted ?? false;
            $conv->is_pinned     = $participant?->is_pinned ?? false;
            $conv->last_message  = $conv->messages->first();
            $conv->reply_permission_value = $conv->reply_permission;
            $conv->reply_permission = $conv->canUserSend($user) ?? 0;

            // For single chats, expose the other user as the "title"
            if ($conv->type === 'single') {
                $other = $conv->participants->firstWhere('user_id', '!=', $user->id);
                $conv->other_user = $other?->user;
            }
            unset($conv->messages);
            return $conv;
        });



        return response()->json($conversations);
    }

    /**
     * Create an single chat — prevents duplicates per module.
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
        $targetUser = User::where('id', $targetId)->first();

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

                (new FcmNotificationService())->sendNewMessage($uid, [
                    'conversation_id'   => $conversation->id,
                    'sender_name'       => $request->user()->name,
                    'preview'           => Str::limit("New Conversation Created..", 80),
                    'conversation_name' => $conv?->title ?? $request->user()->name,
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

        $authId = config('adminId', 1) ?? $request->user()->id;
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

                (new FcmNotificationService())->sendNewMessage($uid, [
                    'conversation_id'   => $conversation->id,
                    'sender_name'       => $request->user()->name,
                    'preview'           => Str::limit("New Conversation Created..", 80),
                    'conversation_name' => $conv?->title ?? $request->user()->name,
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

        if ($conversation->type === 'single') {
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

            (new FcmNotificationService())->sendNewMessage($uid, [
                'conversation_id'   => $conversation->id,
                'sender_name'       => $request->user()->name,
                'preview'           => Str::limit("New Member Added..", 80),
                'conversation_name' => $conv?->title ?? $request->user()->name,
            ]);
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
        $user = $request->user();

        $isMember = DB::table('conversation_participants')
            ->where('conversation_id', $conversationId)
            ->where('user_id', $user->id)
            ->exists();

        if (!$isMember) {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }

        $type = $request->query('type', 'media');

        $query = DB::table('messages')
            ->leftJoin(
                'media_files',
                'media_files.id',
                '=',
                'messages.media_url'
            )
            ->where('messages.conversation_id', $conversationId)
            ->where('messages.is_deleted', 0)
            ->whereNull('messages.deleted_at')
            ->orderByDesc('messages.id');

        switch ($type) {

            case 'media':
                $query->whereIn('messages.type', [
                    'image',
                    'video',
                    'audio',
                    'voice'
                ]);
                break;

            case 'links':
                $query->where('messages.type', 'link');
                break;

            case 'docs':
                $query->where('messages.type', 'file');
                break;

            default:
                return response()->json([
                    'message' => 'Invalid type'
                ], 422);
        }

        $rows = $query->get([
            'messages.id',
            'messages.type',
            'messages.message',
            'messages.media_url',
            'messages.created_at',

            'media_files.file_name',
            'media_files.file_path',
            'media_files.file_size',
            'media_files.file_type',
        ]);

        $data = $rows->map(function ($row) use ($type) {

            $base = [
                'id'      => $row->id,
                'type'    => $row->type,
                'sent_at' => $row->created_at,
            ];

            $fileUrl = $row->file_path
                ? Storage::disk('public')->url($row->file_path)
                : null;

            if ($type === 'media') {

                return array_merge($base, [
                    'media_url' => $fileUrl,
                    'thumb_url' => $fileUrl,
                ]);
            }

            if ($type === 'links') {

                $url = $row->message;

                $domain = null;

                if (!empty($url)) {
                    $parsed = parse_url($url);
                    $domain = $parsed['host'] ?? null;
                }

                return array_merge($base, [
                    'link_title'  => null,
                    'link_url'    => $url,
                    'link_domain' => $domain,
                ]);
            }

            if ($type === 'docs') {

                $fileName = $row->file_name
                    ?: basename(parse_url($fileUrl, PHP_URL_PATH) ?? '');

                $fileExt = strtolower(
                    pathinfo($fileName, PATHINFO_EXTENSION)
                );

                return array_merge($base, [
                    'file_name' => $fileName,
                    'file_ext'  => $fileExt,
                    'file_size' => $row->file_size,
                    'media_url' => $fileUrl,
                ]);
            }

            return $base;
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }





    // app/Http/Controllers/Chat/ConversationController.php

    // 1 — Full detail: permission + member list with role/phone/avatar
    public function detail(Request $request, int $id)
    {
        $conv = Conversation::with(['participants.user:id,name,phone,avatar,acc_type'])->findOrFail($id);
        $user = $request->user();

        abort_unless(
            $conv->participants->contains('user_id', $user->id),
            403,
            'Not a participant.'
        );

        return response()->json([
            'conversation' => [
                'id'               => $conv->id,
                'title'            => $conv->title,
                'avatar'           => $conv->avatar,
                'type'             => $conv->type,
                'status'           => $conv->status,
                'reply_permission' => $conv->reply_permission,
                'created_by'       => $conv->created_by,
                'total_members'    => $conv->participants->where('status', 'active')->count(),
                'participants'     => $conv->participants->where('status', 'active')->map(fn($p) => [
                    'id'     => $p->user->id,
                    'name'   => $p->user->name,
                    'phone'  => $p->user->phone,
                    'avatar' => $p->user->avatar_url ?? null,
                    'role'   => $p->user->acc_type,
                    'is_creator' => $p->user->id === $conv->created_by,
                ])->values(),
            ],
        ]);
    }

    // 2 — Update reply permission only (admin/staff)
    public function updateReplyPermission(Request $request, int $id)
    {
        $conv = Conversation::findOrFail($id);
        $user = $request->user();

        if (!in_array($user->acc_type, ['admin', 'staff'])) {
            return response()->json(['status' => false, 'message' => 'Not authorized.'], 403);
        }

        $validated = $request->validate([
            'reply_permission' => 'required|in:admin,staff,teacher,all',
        ]);

        $conv->update($validated);

        // broadcast(new \App\Events\ConversationUpdated($conv->id, ['reply_permission' => $conv->reply_permission]))->toOthers();

        return response()->json(['status' => true, 'reply_permission' => $conv->reply_permission]);
    }

    // 3 — Sync participants: replace full member set (used by "All Teachers/Students/Staff" auto-sync + custom multi-select)
    public function syncParticipants(Request $request, int $id)
    {
        $conv = Conversation::findOrFail($id);
        $user = $request->user();

        if (!in_array($user->acc_type, ['admin', 'staff'])) {
            return response()->json(['status' => false, 'message' => 'Not authorized.'], 403);
        }

        $validated = $request->validate([
            'user_ids'   => 'required|array|min:1',
            'user_ids.*' => 'integer|exists:users,id',
        ]);

        DB::transaction(function () use ($conv, $validated) {
            $incoming = collect($validated['user_ids']);
            $current  = $conv->participants()->pluck('user_id');

            $toAdd    = $incoming->diff($current);
            $toRemove = $current->diff($incoming);

            foreach ($toAdd as $uid) {
                $conv->participants()->create(['user_id' => $uid, 'status' => 'active']);
            }

            if ($toRemove->isNotEmpty()) {
                $conv->participants()->whereIn('user_id', $toRemove)
                    ->update(['status' => 'removed']);
            }

            // reactivate anyone previously removed but now re-included
            $conv->participants()
                ->whereIn('user_id', $incoming)
                ->where('status', '!=', 'active')
                ->update(['status' => 'active']);
        });

        $updated = $conv->fresh(['participants.user:id,name,phone,avatar,acc_type']);

        // broadcast(new \App\Events\ConversationUpdated($conv->id, ['participants_changed' => true]))->toOthers();

        return response()->json([
            'status'  => true,
            'total_members' => $updated->participants->where('status', 'active')->count(),
            'participants'  => $updated->participants->where('status', 'active')->map(fn($p) => [
                'id' => $p->user->id,
                'name' => $p->user->name,
                'phone' => $p->user->phone,
                'avatar' => $p->user->avatar_url,
                'role' => $p->user->acc_type,
            ])->values(),
        ]);
    }

    // 4 — Group status edit (active/suspended/expired/declined)
    public function updateStatus(Request $request, int $id)
    {
        $conv = Conversation::findOrFail($id);
        $user = $request->user();

        if (!in_array($user->acc_type, ['admin', 'staff'])) {
            return response()->json(['status' => false, 'message' => 'Not authorized.'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:active,suspended,expired,declined',
        ]);

        $conv->update($validated);
        // broadcast(new \App\Events\ConversationUpdated($conv->id, ['status' => $conv->status]))->toOthers();

        return response()->json(['status' => true, 'conversation_status' => $conv->status]);
    }

    // 5 — Remove group entirely
    public function destroy(Request $request, int $id)
    {
        $conv = Conversation::findOrFail($id);
        $user = $request->user();
        // $conv->created_by !== $user->id &&
        if ($user->acc_type !== 'admin') {
            return response()->json(['status' => false, 'message' => 'Not authorized.'], 403);
        }

        // broadcast(new \App\Events\ConversationDeleted($conv->id))->toOthers();
        $conv->delete(); // add cascading deletes for messages/participants at DB level if not already set

        return response()->json(['status' => true, 'message' => 'Group removed.']);
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
    //         'sent_at'     => $m->created_at?->toIso8601String(true),
    //     ]);

    //     return response()->json(['data' => $items]);
    // }
}
