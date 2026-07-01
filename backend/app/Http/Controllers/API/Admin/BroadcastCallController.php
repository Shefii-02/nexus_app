<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Call;
use App\Models\CallRecipient;
use App\Models\Course;
use App\Services\Notification\CallNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BroadcastCallController extends Controller
{
    public function store(Request $request, Course $course)
    {
        $data = $request->validate([
            'student_ids'   => ['required', 'array', 'min:1'],
            'student_ids.*' => ['integer', 'exists:users,id'],
            'type'          => ['sometimes', Rule::in(['audio', 'video'])],
        ]);

        $teacher    = $request->user();
        $studentIds = array_unique($data['student_ids']);

        $result = DB::transaction(function () use ($course, $teacher, $studentIds, $data) {
            $busy = CallRecipient::whereIn('student_id', $studentIds)
                ->whereIn('status', CallRecipient::ACTIVE_STATUSES)
                ->lockForUpdate()
                ->pluck('student_id')
                ->all();

            $callable = array_values(array_diff($studentIds, $busy));

            if (empty($callable)) {
                return ['call' => null, 'busy' => $busy, 'called' => []];
            }

            $call = Call::create([
                'course_id'   => $course->id,
                'teacher_id'  => $teacher->id,
                'caller_role' => 'teacher',
                'caller_id'   => $teacher->id,
                'student_id'  => null,
                'type'        => $data['type'] ?? 'audio',
                'status'      => 'ringing',
                'started_at'  => now(),
            ]);

            CallRecipient::insert(
                collect($callable)->map(fn ($id) => [
                    'call_id'    => $call->id,
                    'student_id' => $id,
                    'status'     => 'ringing',
                    'created_at' => now(),
                    'updated_at' => now(),
                ])->all()
            );

            return ['call' => $call, 'busy' => $busy, 'called' => $callable];
        });

        if (!$result['call']) {
            return response()->json([
                'status'  => 'failed',
                'message' => 'All selected students are currently on another call.',
            ], 409);
        }

        // ── Push to each called student ───────────────────────────────────
        (new CallNotificationService())->notifyUsers($result['called'], [
            'type'         => 'incoming_call',
            'call_id'      => (string) $result['call']->id,
            'caller_name'  => $teacher->name,
            'caller_id'    => (string) $teacher->id,
            'course_id'    => (string) $course->id,
            'course_name'  => $course->name ?? '',
            'call_type'    => $result['call']->type,
            'caller_role'  => 'teacher',
            'voice_msg'    => 'assets/sounds/teacher_waiting_for_you.mp3',
        ]);

        return response()->json([
            'status'             => count($result['busy']) ? 'partial' : 'success',
            'call_id'            => $result['call']->id,
            'called_student_ids' => $result['called'],
            'busy_student_ids'   => $result['busy'],
            'message'            => count($result['busy'])
                ? count($result['busy']) . ' student(s) were already on a call and were not rung.'
                : 'Calling ' . count($result['called']) . ' student(s)…',
        ], 201);
    }

    public function answer(Request $request, Call $call, $studentId)
    {
        $recipient = $call->recipients()
            ->where('student_id', $request->user()->id)
            ->firstOrFail();

        if ($recipient->status !== 'ringing') {
            return response()->json(['status' => 'failed', 'message' => 'Call already resolved.'], 409);
        }

        $recipient->update(['status' => 'answered', 'answered_at' => now()]);

        if ($call->status === 'ringing') {
            $call->update(['status' => 'active']);
        }

        return response()->json(['status' => 'success']);
    }

    public function reject(Request $request, Call $call, $studentId)
    {
        $recipient = $call->recipients()
            ->where('student_id', $request->user()->id)
            ->firstOrFail();

        if ($recipient->status === 'ringing') {
            $recipient->update(['status' => 'rejected', 'ended_at' => now()]);
        }

        return response()->json(['status' => 'success']);
    }

    public function end(Request $request, Call $call)
    {
        if ($call->teacher_id !== $request->user()->id) {
            abort(403);
        }

        DB::transaction(function () use ($call) {
            $call->recipients()
                ->whereIn('status', ['ringing'])
                ->update(['status' => 'cancelled', 'ended_at' => now()]);

            $call->recipients()
                ->where('status', 'answered')
                ->update(['status' => 'ended', 'ended_at' => now()]);

            $call->update(['status' => 'ended', 'ended_at' => now()]);
        });

        return response()->json(['status' => 'success']);
    }
}
