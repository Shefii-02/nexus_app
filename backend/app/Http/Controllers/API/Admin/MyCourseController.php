<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\MyCourseDetailResource;
use App\Http\Resources\MyCourseResource;
use App\Models\Course;
use App\Models\CourseClass;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MyCourseController extends Controller
{

    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->acc_type == 'teacher') {
            $myCourses = Course::with(['teachers', 'classes'])
                ->whereHas('teachers', function ($q) use ($user) {
                    $q->where('teacher_id', $user->id);
                })->get();
        } else {
            $myCourses = Course::with(['students', 'classes'])
                ->whereHas('students', function ($q) use ($user) {
                    $q->where('student_id', $user->id); // was teacher_id — bug
                })->get();
        }

        return response()->json([
            'success' => true,
            'data'    => MyCourseResource::collection($myCourses),
        ]);
    }

    // MyCourseController.php

    public function single(Request $request, int $id)
    {
        $user = $request->user();

        $course = Course::with([
            'teachers',
            'classes' => fn($q) => $q->whereNull('deleted_at')->orderBy('scheduled_date'),
            'materials' => fn($q) => $q->whereNull('deleted_at')->orderBy('order'),
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => new MyCourseDetailResource($course),
        ]);
    }

    public function today(Request $request)
    {
        $user = $request->user();
        $now  = Carbon::now();
        $todayStart = $now->copy()->startOfDay();
        $todayEnd   = $now->copy()->endOfDay();

        $query = CourseClass::with(['course', 'teacher.user'])
            ->whereNull('deleted_at')
            ->where(function ($q) use ($todayStart, $todayEnd) {
                // classes that start OR end today
                $q->whereBetween('started_at', [$todayStart, $todayEnd])
                    ->orWhereBetween('ended_at',   [$todayStart, $todayEnd])
                    // also catch classes that span the whole day
                    ->orWhere(function ($q2) use ($todayStart, $todayEnd) {
                        $q2->where('started_at', '<=', $todayStart)
                            ->where('ended_at',   '>=', $todayEnd);
                    });
            });

        // ── scope by role ──────────────────────────────────────────────────
        if ($user->acc_type === 'teacher') {
            $query->where('teacher_id', $user->id);
        } else {
            // student: only classes belonging to courses they're enrolled in
            $query->whereHas('course.students', function ($q) use ($user) {
                $q->where('student_id', $user->id);
            });
        }

        $classes = $query
            ->orderBy('started_at')
            ->get()
            ->map(fn($class) => $this->formatClass($class, $now));

        return response()->json(['data' => $classes]);
    }

    private function formatClass(CourseClass $class, Carbon $now): array
    {
        $start = $class->started_at ? Carbon::parse($class->started_at) : null;
        $end   = $class->ended_at   ? Carbon::parse($class->ended_at)   : null;

        // Compute live status dynamically (don't trust stored status alone)
        $status = 'upcoming';
        if ($class->status == '1' && $start && $end) {
            if ($now->lt($start)) {
                $status = 'upcoming';
            } elseif ($now->between($start, $end)) {
                $status = 'live';
            } else {
                $status = 'completed';
            }
        } elseif ($class->status == '0') {
            $status = 'draft';
        } elseif ($class->status == '2') {
            $status = 'cancelled';
        }

        $teacher = $class->teacher?->user ?? $class->teacher;

        return [
            'id'          => $class->id,
            'title'       => $class->title,
            'description' => $class->description,
            'start_time'  => $start?->toISOString(),
            'end_time'    => $end?->toISOString(),
            'status'      => $status,
            'class_link'  => $class->class_link,
            'record_link' => $class->record_link,
            'teacher'     => $teacher ? [
                'id'     => $teacher->id,
                'name'   => $teacher->name,
                'avatar' => $teacher->avatar_url ?? null,
            ] : null,
        ];
    }
}
