<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConversationRepairController extends Controller
{
    private const ADMIN_ID = 1;

    // One-time-use secret so this URL isn't guessable/public.
    // Replace with your own random string, or better, pull from .env.
    private const RUN_SECRET = 'nexus-fix-2026-07-27';

    public function run(string $secret): JsonResponse
    {
        if (!hash_equals(self::RUN_SECRET, $secret)) {
            abort(403);
        }

        $adminId = self::ADMIN_ID;
        $fixed = [];
        $skippedComplete = [];
        $skippedNoConversation = [];

        $users = User::where('id', '!=', $adminId)->get();

        DB::beginTransaction();
        try {
            foreach ($users as $user) {
                // ── Case 1: already correctly has BOTH user + admin ──────
                $completeConversation = Conversation::where('type', 'single')
                    ->whereHas('participants', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    })
                    ->whereHas('participants', function ($q) use ($adminId) {
                        $q->where('user_id', $adminId);
                    })
                    ->withCount('participants')
                    ->having('participants_count', 2)
                    ->first();

                if ($completeConversation) {
                    $skippedComplete[] = $user->id;
                    continue;
                }

                // ── Case 2: user has a single conversation, but admin
                //    was removed from it — this is the repair target ────
                $brokenConversation = Conversation::where('type', 'single')
                    ->whereHas('participants', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    })
                    ->whereDoesntHave('participants', function ($q) use ($adminId) {
                        $q->where('user_id', $adminId);
                    })
                    ->first();

                if (!$brokenConversation) {
                    // No single conversation exists for this user at all —
                    // out of scope for a repair pass, so skip.
                    $skippedNoConversation[] = $user->id;
                    continue;
                }

                ConversationParticipant::firstOrCreate(
                    [
                        'conversation_id' => $brokenConversation->id,
                        'user_id'         => $adminId,
                    ],
                    [
                        'created_by' => $adminId,
                        'status'     => 'active',
                    ]
                );

                $fixed[] = [
                    'user_id'         => $user->id,
                    'conversation_id' => $brokenConversation->id,
                ];
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Conversation participant repair failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Repair failed',
                'error'   => $e->getMessage(),
            ], 500);
        }

        Log::info('Conversation participant repair completed', [
            'fixed_count'   => count($fixed),
            'skipped_ok'    => count($skippedComplete),
            'skipped_none'  => count($skippedNoConversation),
        ]);

        return response()->json([
            'status'                  => true,
            'fixed_count'             => count($fixed),
            'fixed'                   => $fixed,
            'skipped_already_ok'      => $skippedComplete,
            'skipped_no_conversation' => $skippedNoConversation,
        ]);
    }
}
