<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\DTOs\MessageDTO;
use App\Http\Requests\MessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\DeletedMessage;
use App\Models\Message;
use App\Models\MessageReport;
use App\Services\Message\MessageService;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function __construct(private MessageService $service) {}

    public function store(MessageRequest $request)
    {
        $dto = MessageDTO::fromArray($request->validated());
        $message = $this->service->send($dto);

        return new MessageResource($message);
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'message' => 'required|string|max:5000'
        ]);

        $message = Message::findOrFail($id);

        if ($message->sender_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $message->update([
            'message' => $request->message,
            'is_edited' => true
        ]);

        return response()->json(['data' => $message]);
    }

    public function destroy(Request $request, int $id)
    {
        $forEveryone = $request->boolean('for_everyone');

        $this->service->delete($id, $forEveryone);

        return response()->json([
            'status' => true,
            'message' => 'Message deleted'
        ]);
    }

    public function report(Request $request, int $id)
    {
        MessageReport::create([
            'message_id' => $id,
            'user_id' => auth()->id(),
            'reason' => $request->reason
        ]);

        return response()->json(['message' => 'Reported']);
    }

    public function pin(int $id)
    {
        $msg = Message::findOrFail($id);

        $msg->update([
            'is_pinned' => !$msg->is_pinned
        ]);

        return response()->json(['data' => $msg]);
    }


}
