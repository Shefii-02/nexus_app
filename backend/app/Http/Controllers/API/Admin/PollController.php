<?php

namespace App\Http\Controllers\API\Admin;

use App\Chat\Events\PollClosed;
use App\Chat\Events\PollVoteCast;
use App\Chat\Models\Message;
use App\Http\Controllers\Controller;
use App\Models\{Conversation, ConversationParticipant, Poll, PollOption, PollVote};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PollController extends Controller
{
    // ── Create a poll (= a message of type 'poll') ──────────────────────────
    public function store(Request $request, int $conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);
        $user = $request->user();

        $isMember = ConversationParticipant::where('conversation_id', $conversationId)
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->exists();

        if (!$isMember) {
            return response()->json(['message' => 'Not a participant in this conversation.'], 403);
        }

        // Poll creation follows the same reply_permission gate as normal messages
        if (!$conversation->canUserSend($user)) {
            return response()->json([
                'message' => 'You do not have permission to create polls in this conversation.',
            ], 403);
        }

        $validated = $request->validate([
            'question'              => 'required|string|max:500',
            'options'                => 'required|array|min:2|max:10',
            'options.*'              => 'required|string|max:255',
            'allow_multiple_votes'   => 'sometimes|boolean',
            'closes_at'              => 'nullable|date|after:now',
        ]);

        $result = DB::transaction(function () use ($validated, $conversation, $user) {
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id'       => $user->id,
                'type'            => 'poll',
                'message'         => null,
            ]);

            $poll = Poll::create([
                'message_id'            => $message->id,
                'conversation_id'       => $conversation->id,
                'created_by'            => $user->id,
                'question'              => $validated['question'],
                'allow_multiple_votes'  => $validated['allow_multiple_votes'] ?? false,
                'closes_at'             => $validated['closes_at'] ?? null,
            ]);

            foreach ($validated['options'] as $i => $text) {
                PollOption::create([
                    'poll_id'     => $poll->id,
                    'option_text' => $text,
                    'sort_order'  => $i,
                ]);
            }

            return $message->load('poll.options', 'sender');
        });

        broadcast(new \App\Chat\Events\MessageSent($result))->toOthers();

        return response()->json(['message' => $result], 201);
    }

    // ── Cast / change a vote ──────────────────────────────────────────────
    // public function vote(Request $request, int $pollId)
    // {
    //     $poll = Poll::with('options')->findOrFail($pollId);
    //     $user = $request->user();

    //     $isMember = ConversationParticipant::where('conversation_id', $poll->conversation_id)
    //         ->where('user_id', $user->id)
    //         ->where('status', 'active')
    //         ->exists();

    //     if (!$isMember) {
    //         return response()->json(['message' => 'Not a participant in this conversation.'], 403);
    //     }

    //     // Voting is allowed for everyone, same as reactions — no reply_permission check here
    //     if ($poll->is_closed || ($poll->closes_at && $poll->closes_at->isPast())) {
    //         return response()->json(['message' => 'This poll is closed.'], 422);
    //     }

    //     $validated = $request->validate([
    //         'option_id' => 'required|integer|exists:poll_options,id',
    //     ]);

    //     $option = $poll->options->firstWhere('id', $validated['option_id']);
    //     if (!$option) {
    //         return response()->json(['message' => 'Option does not belong to this poll.'], 422);
    //     }

    //     DB::transaction(function () use ($poll, $option, $user) {
    //         $existingVote = PollVote::where('poll_id', $poll->id)
    //             ->where('poll_option_id', $option->id)
    //             ->where('user_id', $user->id)
    //             ->first();

    //         if ($existingVote) {
    //             // tapping the same option again removes the vote (toggle)
    //             $existingVote->delete();
    //             return;
    //         }

    //         if (!$poll->allow_multiple_votes) {
    //             // single-choice poll — clear any other vote by this user first
    //             PollVote::where('poll_id', $poll->id)->where('user_id', $user->id)->delete();
    //         }

    //         PollVote::create([
    //             'poll_id'        => $poll->id,
    //             'poll_option_id' => $option->id,
    //             'user_id'        => $user->id,
    //         ]);
    //     });

    //     // Only broadcast aggregate counts — never voter identities — since
    //     // every participant (including students) shares this channel.
    //     $tally = $this->aggregateTally($poll->fresh());

    //     broadcast(new PollVoteCast($poll->conversation_id, $poll->id, $tally))->toOthers();

    //     return response()->json(['tally' => $tally]);
    // }
    public function vote(Request $request, int $pollId)
    {
        $poll = Poll::with('options')->findOrFail($pollId);

        $user = $request->user();

        /*
    |--------------------------------------------------------------------------
    | Check conversation membership
    |--------------------------------------------------------------------------
    */

        $isMember = ConversationParticipant::query()
            ->where('conversation_id', $poll->conversation_id)
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->exists();

        if (!$isMember) {
            return response()->json([
                'message' => 'Not a participant in this conversation.',
            ], 403);
        }

        /*
    |--------------------------------------------------------------------------
    | Check poll status
    |--------------------------------------------------------------------------
    */

        if (
            $poll->is_closed ||
            (
                $poll->closes_at &&
                $poll->closes_at->isPast()
            )
        ) {
            return response()->json([
                'message' => 'This poll is closed.',
            ], 422);
        }

        /*
    |--------------------------------------------------------------------------
    | Validate option
    |--------------------------------------------------------------------------
    */

        $validated = $request->validate([
            'option_id' => [
                'required',
                'integer',
                'exists:poll_options,id',
            ],
        ]);

        /*
    |--------------------------------------------------------------------------
    | Make sure option belongs to this poll
    |--------------------------------------------------------------------------
    */

        $option = $poll->options
            ->firstWhere('id', (int) $validated['option_id']);

        if (!$option) {
            return response()->json([
                'message' => 'Option does not belong to this poll.',
            ], 422);
        }

        /*
    |--------------------------------------------------------------------------
    | Create / remove / change vote
    |--------------------------------------------------------------------------
    */

        DB::transaction(function () use ($poll, $option, $user) {

            /*
        |--------------------------------------------------------------------------
        | Same option clicked again = remove vote
        |--------------------------------------------------------------------------
        */

            $existingVote = PollVote::query()
                ->where('poll_id', $poll->id)
                ->where('poll_option_id', $option->id)
                ->where('user_id', $user->id)
                ->first();

            if ($existingVote) {
                $existingVote->delete();

                return;
            }

            /*
        |--------------------------------------------------------------------------
        | Single-choice poll
        |--------------------------------------------------------------------------
        |
        | Delete any previous vote by this user
        |
        */

            if (!$poll->allow_multiple_votes) {
                PollVote::query()
                    ->where('poll_id', $poll->id)
                    ->where('user_id', $user->id)
                    ->delete();
            }

            /*
        |--------------------------------------------------------------------------
        | Create new vote
        |--------------------------------------------------------------------------
        */

            PollVote::create([
                'poll_id'        => $poll->id,
                'poll_option_id' => $option->id,
                'user_id'        => $user->id,
            ]);
        });

        /*
    |--------------------------------------------------------------------------
    | Recalculate complete tally
    |--------------------------------------------------------------------------
    */

        $tally = $this->aggregateTally(
            $poll->fresh()
        );

        /*
    |--------------------------------------------------------------------------
    | Broadcast to all OTHER users
    |--------------------------------------------------------------------------
    |
    | The current user receives the API response.
    | Other users receive poll.voted through Reverb.
    |
    */

        broadcast(
            new PollVoteCast(
                conversationId: $poll->conversation_id,
                pollId: $poll->id,
                tally: $tally,
            )
        )->toOthers();

        /*
    |--------------------------------------------------------------------------
    | Return current user's result
    |--------------------------------------------------------------------------
    */

        return response()->json([
            'tally' => $tally,
        ]);
    }

    // ── Aggregate counts only — safe for every participant ─────────────────
    public function results(int $pollId)
    {
        $poll = Poll::with('options')->findOrFail($pollId);
        return response()->json(['tally' => $this->aggregateTally($poll)]);
    }

    // ── Full voter breakdown — admin/staff only ─────────────────────────────
    public function voters(Request $request, int $pollId)
    {
        // $poll = Poll::with(['options.votes.user:id,name,avatar,phone,options.votes.user.media'])->findOrFail($pollId);
        $poll = Poll::with([
            'options.votes.user:id,name,phone,avatar',   // ✅ id + phone + avatar FK column
            'options.votes.user.media',                   // ✅ separate entry — nested relation
        ])->findOrFail($pollId);
        $user = $request->user();

        // if (!in_array($user->role, ['admin', 'staff'])) {
        //     return response()->json(['message' => 'Not authorized to view voter details.'], 403);
        // }

        $isMember = ConversationParticipant::where('conversation_id', $poll->conversation_id)
            ->where('user_id', $user->id)
            ->exists();
        if (!$isMember) {
            return response()->json(['message' => 'Not a participant in this conversation.'], 403);
        }

        return response()->json([
            'poll' => [
                'id' => $poll->id,
                'question' => $poll->question,
                'allow_multiple_votes' => $poll->allow_multiple_votes,
                'total_voters' => $poll->totalVoters(),
                'options' => $poll->options->map(fn($opt) => [
                    'id'    => $opt->id,
                    'text'  => $opt->option_text,
                    'count' => $opt->votes->count(),
                    'voters' => $opt->votes->map(fn($v) => [
                        'id'     => $v->user->id,
                        'name'   => $v->user->name,
                        'phone'  => $v->user->phone,
                        'avatar' => $v->user->avatar_url,
                        'voted_at' => $v->created_at,
                    ]),
                ]),
            ],
        ]);
    }

    // public function close(Request $request, int $pollId)
    // {
    //     $poll = Poll::findOrFail($pollId);
    //     $user = $request->user();

    //     if ($poll->created_by !== $user->id && !in_array($user->role, ['admin', 'staff'])) {
    //         return response()->json(['message' => 'Not authorized to close this poll.'], 403);
    //     }

    //     $poll->update(['is_closed' => true]);
    //     broadcast(new PollClosed($poll->conversation_id, $poll->id))->toOthers();

    //     return response()->json(['status' => 'closed']);
    // }
    public function close(Request $request, int $pollId)
    {
        $poll = Poll::findOrFail($pollId);

        $user = $request->user();

        /*
    |--------------------------------------------------------------------------
    | Only poll creator, admin, or staff can close
    |--------------------------------------------------------------------------
    */

        if (
            $poll->created_by !== $user->id &&
            !in_array($user->role, ['admin', 'staff'], true)
        ) {
            return response()->json([
                'message' => 'Not authorized to close this poll.',
            ], 403);
        }

        /*
    |--------------------------------------------------------------------------
    | Close poll
    |--------------------------------------------------------------------------
    */

        $poll->update([
            'is_closed' => true,
        ]);

        /*
    |--------------------------------------------------------------------------
    | Broadcast immediately
    |--------------------------------------------------------------------------
    */

        broadcast(
            new PollClosed(
                conversationId: $poll->conversation_id,
                pollId: $poll->id,
            )
        )->toOthers();

        return response()->json([
            'status' => 'closed',
        ]);
    }

    private function aggregateTally(Poll $poll): array
    {
        return [
            'poll_id'      => $poll->id,
            'total_voters' => $poll->totalVoters(),
            'options'      => $poll->options()->withCount('votes')->get()
                ->map(fn($o) => ['id' => $o->id, 'count' => $o->votes_count]),
        ];
    }
}
