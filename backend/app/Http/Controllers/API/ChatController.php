<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $user = auth()->user();
        $page = request()->query('page', 1);
        $perPage = request()->query('per_page', 15);

        $conversations = $user->conversations()
            ->with(['participants', 'messages' => function ($query) {
                $query->latest()->limit(1);
            }])
            ->paginate($perPage, ['*'], 'page', $page);

        return $this->paginatedResponse($conversations, 'Chats retrieved successfully');
    }

    public function show(int $chat): JsonResponse
    {
        $user = auth()->user();
        $conversation = Conversation::whereHas('participants', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->with(['participants', 'messages.sender'])
            ->find($chat);

        if (!$conversation) {
            return $this->errorResponse('Chat not found or access denied', null, 404);
        }

        return $this->successResponse($conversation, 'Chat retrieved successfully');
    }

    public function store(Request $request): JsonResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'type' => 'required|in:single,group',
            'participant_ids' => 'required|array|min:1',
            'participant_ids.*' => 'integer|exists:users,id',
            'title' => 'nullable|string|max:255',
        ]);

        if ($validated['type'] === 'single' && count($validated['participant_ids']) !== 1) {
            return $this->errorResponse('Single chat requires exactly one participant', null, 422);
        }

        $conversation = Conversation::create([
            'type' => $validated['type'],
            'title' => $validated['title'] ?? null,
            'created_by' => $user->id,
        ]);

        $participantIds = array_unique(array_merge($validated['participant_ids'], [$user->id]));
        $conversation->participants()->sync($participantIds);

        return $this->successResponse($conversation->load(['participants']), 'Chat created successfully', 201);
    }

    public function sendMessage(Request $request, int $chat): JsonResponse
    {
        $user = auth()->user();

        $conversation = Conversation::whereHas('participants', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->find($chat);

        if (!$conversation) {
            return $this->errorResponse('Chat not found or access denied', null, 404);
        }

        $validated = $request->validate([
            'message' => 'nullable|string',
            'type' => 'required|in:text,image,file,audio,video,class_link,record_link',
            'media_url' => 'nullable|string|max:1000',
            'reply_to' => 'nullable|integer|exists:messages,id',
        ]);

        if (empty($validated['message']) && empty($validated['media_url'])) {
            return $this->errorResponse('Message text or media URL is required', null, 422);
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'message' => $validated['message'] ?? null,
            'type' => $validated['type'],
            'media_url' => $validated['media_url'] ?? null,
            'reply_to' => $validated['reply_to'] ?? null,
        ]);

        return $this->successResponse($message->load('sender'), 'Message sent successfully', 201);
    }
}
