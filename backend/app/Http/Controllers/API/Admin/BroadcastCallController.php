<?php
// app/Http/Controllers/Api/BroadcastCallController.php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Call;
use App\Models\CallRecipient;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BroadcastCallController extends Controller
{
    /**
     * POST /api/course/{course}/call
     * Teacher calls a set of selected students.
     */
    public function store(Request $request, Course $course)
    {
        $data = $request->validate([
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['integer', 'exists:users,id'],
            'type' => ['sometimes', Rule::in(['audio', 'video'])],
        ]);

        $teacher = $request->user();
        $studentIds = array_unique($data['student_ids']);

        $result = DB::transaction(function () use ($course, $teacher, $studentIds, $data) {
            // Lock any students who are already ringing/answered on another
            // active call so we don't double-ring someone mid-call.
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
                'course_id' => $course->id,
                'teacher_id' => $teacher->id,
                'caller_role' => 'teacher',
                'caller_id' => $teacher->id,
                'student_id' => null,
                'type' => $data['type'] ?? 'audio',
                'status' => 'ringing',
                'started_at' => now(),
            ]);

            $rows = collect($callable)->map(fn ($id) => [
                'call_id' => $call->id,
                'student_id' => $id,
                'status' => 'ringing',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            CallRecipient::insert($rows->all());

            return ['call' => $call, 'busy' => $busy, 'called' => $callable];
        });

        if (!$result['call']) {
            return response()->json([
                'status' => 'failed',
                'message' => 'All selected students are currently on another call.',
            ], 409);
        }

        // TODO: dispatch FCM/CallKit push to each id in $result['called'],
        // same as the existing class-alert push pipeline.

        return response()->json([
            'status' => count($result['busy']) ? 'partial' : 'success',
            'call_id' => $result['call']->id,
            'called_student_ids' => $result['called'],
            'busy_student_ids' => $result['busy'],
            'message' => count($result['busy'])
                ? count($result['busy']).' student(s) were already on a call and were not rung.'
                : 'Calling '.count($result['called']).' student(s)…',
        ], 201);
    }

    /** POST /api/calls/{call}/recipients/{student}/answer */
    public function answer(Request $request, Call $call, $studentId)
    {
        $recipient = $call->recipients()
            ->where('student_id', $studentId)
            ->where('student_id', $request->user()->id) // student answers for themself
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

    /** POST /api/calls/{call}/recipients/{student}/reject */
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

    /** POST /api/calls/{call}/end — teacher ends the whole broadcast */
    public function end(Request $request, Call $call)
    {
        if ($call->teacher_id !== $request->user()->id) {
            abort(403);
        }

        DB::transaction(function () use ($call) {
            $call->recipients()
                ->where('status', 'ringing')
                ->update(['status' => 'cancelled', 'ended_at' => now()]);

            $call->recipients()
                ->where('status', 'answered')
                ->update(['status' => 'ended', 'ended_at' => now()]);

            $call->update(['status' => 'ended', 'ended_at' => now()]);
        });

        return response()->json(['status' => 'success']);
    }
}
