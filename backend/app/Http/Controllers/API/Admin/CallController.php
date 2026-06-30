<?php
// app/Http/Controllers/Api/CallController.php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Call;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CallController extends Controller
{
    /** How long a ringing call is considered "live" before we treat it as stale/missed. */
    private const RING_TIMEOUT_SECONDS = 30;

    /**
     * POST /api/courses/{course}/call
     * Student initiates a call to a teacher for this course.
     */
    public function store(Request $request, Course $course)
    {
        $data = $request->validate([
            'teacher_id' => ['required', 'exists:users,id'],
            'type'       => ['sometimes', Rule::in(['audio', 'video'])],
            'conversation_id' => ['nullable', 'exists:conversations,id'],
        ]);

        $student = $request->user();
        $teacherId = $data['teacher_id'];

        try {
            $call = DB::transaction(function () use ($course, $student, $teacherId, $data) {
                // Lock any active/ringing calls for this teacher so concurrent
                // requests from two students can't both pass the check.
                $existing = Call::where('teacher_id', $teacherId)
                    ->whereIn('status', Call::ACTIVE_STATUSES)
                    ->lockForUpdate()
                    ->first();

                if ($existing) {
                    $staleRinging = $existing->status === 'ringing'
                        && $existing->started_at?->lt(now()->subSeconds(self::RING_TIMEOUT_SECONDS));

                    if ($staleRinging) {
                        // Previous ring timed out but was never cleaned up
                        // (e.g. caller's app crashed) — mark it missed and proceed.
                        $existing->update([
                            'status' => 'missed',
                            'ended_at' => now(),
                        ]);
                    } else {
                        // Genuinely busy right now.
                        return null;
                    }
                }

                return Call::create([
                    'course_id' => $course->id,
                    'teacher_id' => $teacherId,
                    'student_id' => $student->id,
                    'conversation_id' => $data['conversation_id'] ?? null,
                    'type' => $data['type'] ?? 'audio',
                    'status' => 'ringing',
                    'started_at' => now(),
                ]);
            });
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'status' => 'failed',
                'message' => 'Could not place the call. Please try again.',
            ], 500);
        }

        if (!$call) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Another person is currently calling this teacher.',
            ], 409);
        }

        // TODO: dispatch FCM push to teacher here (incoming-call notification),
        // matching the CallKit flow already wired up on the Flutter side.

        return response()->json([
            'status' => 'success',
            'call_id' => $call->id,
            'message' => 'Calling teacher…',
        ], 201);
    }

    /**
     * POST /api/calls/{call}/answer
     * Teacher accepts the call.
     */
    public function answer(Request $request, Call $call)
    {
        if ($call->teacher_id !== $request->user()->id) {
            abort(403);
        }

        if ($call->status !== 'ringing') {
            return response()->json([
                'status' => 'failed',
                'message' => 'This call is no longer active.',
            ], 409);
        }

        $call->update(['status' => 'active', 'answered_at' => now()]);

        return response()->json(['status' => 'success', 'call' => $call]);
    }

    /**
     * POST /api/calls/{call}/reject
     */
    public function reject(Request $request, Call $call)
    {
        if ($call->teacher_id !== $request->user()->id) {
            abort(403);
        }

        if ($call->status === 'ringing') {
            $call->update(['status' => 'rejected', 'ended_at' => now()]);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * POST /api/calls/{call}/end
     * Either party ends the call (covers your "call end button" + the 30s
     * client-side ring-timeout case, where the client should call this too
     * so the server-side record doesn't stay stuck as "ringing").
     */
    public function end(Request $request, Call $call)
    {
        $userId = $request->user()->id;
        if (!in_array($userId, [$call->teacher_id, $call->student_id])) {
            abort(403);
        }

        if (in_array($call->status, ['ringing', 'active'])) {
            $call->update([
                'status' => $call->status === 'ringing' ? 'missed' : 'ended',
                'ended_at' => now(),
            ]);
        }

        return response()->json(['status' => 'success']);
    }
}
