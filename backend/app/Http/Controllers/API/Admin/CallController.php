<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Call;
use App\Models\Course;
use App\Services\Notification\CallNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CallController extends Controller
{
    private const RING_TIMEOUT_SECONDS = 30;

    public function store(Request $request, Course $course)
    {
        $data = $request->validate([
            'teacher_id'      => ['required', 'exists:users,id'],
            'type'            => ['sometimes', Rule::in(['audio', 'video'])],
            'conversation_id' => ['nullable', 'exists:conversations,id'],
        ]);

        $student   = $request->user();
        $teacherId = $data['teacher_id'];

        try {
            $call = DB::transaction(function () use ($course, $student, $teacherId, $data) {
                $existing = Call::where('teacher_id', $teacherId)
                    ->whereIn('status', Call::ACTIVE_STATUSES)
                    ->lockForUpdate()
                    ->first();

                if ($existing) {
                    $stale = $existing->status === 'ringing'
                        && $existing->started_at?->lt(
                            now()->subSeconds(self::RING_TIMEOUT_SECONDS)
                        );

                    if ($stale) {
                        $existing->update(['status' => 'missed', 'ended_at' => now()]);
                    } else {
                        return null;
                    }
                }

                return Call::create([
                    'course_id'       => $course->id,
                    'teacher_id'      => $teacherId,
                    'student_id'      => $student->id,
                    'conversation_id' => $data['conversation_id'] ?? null,
                    'type'            => $data['type'] ?? 'audio',
                    'status'          => 'ringing',
                    'started_at'      => now(),
                ]);
            });
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'status'  => 'failed',
                'message' => 'Could not place the call. Please try again.',
            ], 500);
        }

        if (!$call) {
            return response()->json([
                'status'  => 'failed',
                'message' => 'Another person is currently calling this teacher.',
            ], 409);
        }

        // ── Push to teacher ───────────────────────────────────────────────
        (new CallNotificationService())->notifyUser($teacherId, [
            'type'         => 'incoming_call',
            'call_id'      => (string) $call->id,
            'caller_name'  => $student->name,
            'caller_id'    => (string) $student->id,
            'course_id'    => (string) $course->id,
            'course_name'  => $course->name ?? '',
            'call_type'    => $call->type,
        ]);

        return response()->json([
            'status'  => 'success',
            'call_id' => $call->id,
            'message' => 'Calling teacher…',
        ], 201);
    }

    public function answer(Request $request, Call $call)
    {
        if ($call->teacher_id !== $request->user()->id) {
            abort(403);
        }
        if ($call->status !== 'ringing') {
            return response()->json([
                'status'  => 'failed',
                'message' => 'This call is no longer active.',
            ], 409);
        }
        $call->update(['status' => 'active', 'answered_at' => now()]);
        return response()->json(['status' => 'success', 'call' => $call]);
    }

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

    public function end(Request $request, Call $call)
    {
        $userId = $request->user()->id;
        if (!in_array($userId, [$call->teacher_id, $call->student_id])) {
            abort(403);
        }
        if (in_array($call->status, ['ringing', 'active'])) {
            $call->update([
                'status'   => $call->status === 'ringing' ? 'missed' : 'ended',
                'ended_at' => now(),
            ]);
        }
        return response()->json(['status' => 'success']);
    }
}
