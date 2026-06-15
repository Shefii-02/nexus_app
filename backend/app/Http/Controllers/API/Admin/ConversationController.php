<?php

namespace App\Http\Controllers\API\Admin;

use App\Chat\Events\MessageSent;
use App\DTOs\ConversationDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\ConversationRequest;
use App\Http\Resources\ConversationDetailResource;
use App\Http\Resources\ConversationResource;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\ConversationReport;
use App\Models\DeletedMessage;
use App\Models\Message;
use App\Services\Conversation\ConversationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function __construct(private ConversationService $service) {}

    public function store(ConversationRequest $request)
    {
        $dto = ConversationDTO::fromArray($request->validated());
        $conversation = $this->service->create($dto);

        return new ConversationResource($conversation);
    }

    public function index()
    {
        return ConversationResource::collection(
            $this->service->list()
        );
    }

    public function show(int $id)
    {
        return new ConversationDetailResource(
            $this->service->show($id)
        );
    }

    public function update(Request $request, int $id)
    {
        $conversation = Conversation::findOrFail($id);

        if ($request->title) {
            $conversation->update(['title' => $request->title]);
        }

        if ($request->add_participants) {
            foreach ($request->add_participants as $userId) {
                ConversationParticipant::firstOrCreate([
                    'conversation_id' => $id,
                    'user_id' => $userId
                ]);
            }
        }

        if ($request->remove_participants) {
            ConversationParticipant::where('conversation_id', $id)
                ->whereIn('user_id', $request->remove_participants)
                ->delete();
        }

        return response()->json(['message' => 'Updated']);
    }

    public function destroy(int $id)
    {
        $this->service->delete($id);

        return response()->json([
            'status' => true,
            'message' => 'Conversation removed'
        ]);
    }


    public function clearForMe(int $conversationId)
    {
        $messages = Message::where('conversation_id', $conversationId)->pluck('id');

        foreach ($messages as $msgId) {
            DeletedMessage::firstOrCreate([
                'message_id' => $msgId,
                'user_id' => auth()->id(),
            ]);
        }
    }

    public function clearGlobal(int $conversationId)
    {
        Message::where('conversation_id', $conversationId)->update([
            'is_deleted' => true,
            'message' => null,
            'media_url' => null
        ]);
    }

    public function toggleMute(int $conversationId)
    {
        $participant = ConversationParticipant::where([
            'conversation_id' => $conversationId,
            'user_id' => auth()->id()
        ])->firstOrFail();

        $participant->update([
            'is_muted' => !$participant->is_muted
        ]);

        return response()->json(['muted' => $participant->is_muted]);
    }


    public function report(Request $request, int $id)
    {
        ConversationReport::create([
            'conversation_id' => $id,
            'user_id' => auth()->id(),
            'reason' => $request->reason
        ]);

        return response()->json(['message' => 'Reported']);
    }

    public function forward(Request $request)
    {
        $request->validate([
            'message_id'       => 'required|integer|exists:messages,id',
            'conversation_ids' => 'required|array|min:1',
            'conversation_ids.*' => 'integer|exists:conversations,id',
        ]);

        $original = \App\Models\Message::findOrFail($request->message_id);
        $user     = $request->user();
        $sent     = [];

        foreach ($request->conversation_ids as $convId) {
            // Verify the user is an active participant in every target conversation
            $isMember = \App\Models\ConversationParticipant::where('conversation_id', $convId)
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->exists();

            if (! $isMember) continue;

            $msg = \App\Models\Message::create([
                'conversation_id' => $convId,
                'sender_id'       => $user->id,
                'message'         => $original->message,
                'type'            => $original->type,
                'media_url'       => $original->media_url,
                'media_meta'      => $original->media_meta,
                'reply_to'        => null,   // forwarded messages don't carry the quote
            ]);

            $msg->load('sender', 'reactions', 'reads');
            broadcast(new MessageSent($msg))->toOthers();
            $sent[] = $msg;
        }

        return response()->json(['messages' => $sent]);
    }
}
