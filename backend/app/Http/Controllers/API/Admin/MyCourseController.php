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

    public function __construct(
        protected CourseClassService    $classService,
        protected CourseMaterialService $materialService,
    ) {}


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


    // ── POST /my_courses/{courseId}/classes ───────────────────────────────────

    public function storeClass(Request $request, int $courseId)
    {
        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'class_number'     => 'nullable|integer',
            'scheduled_date'   => 'nullable|date',
            'started_at'       => 'nullable|date',
            'ended_at'         => 'nullable|date',
            'duration_minutes' => 'nullable|integer',
            'class_link'       => 'nullable|url',
            'record_link'      => 'nullable|url',
            'room_location'    => 'nullable|string|max:255',
            'source'           => 'nullable|in:online,offline',
            'status'           => 'nullable|in:0,1,2',
            'teacher_id'       => 'nullable|exists:teachers,id',
        ]);

        $data['course_id'] = $courseId;
        $class = $this->classService->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Class created successfully.',
            'data'    => $class,
        ], 201);
    }

    // ── PUT /my_courses/classes/{classId} ─────────────────────────────────────

    public function updateClass(Request $request, int $classId)
    {
        $data = $request->validate([
            'title'            => 'sometimes|string|max:255',
            'description'      => 'nullable|string',
            'class_number'     => 'nullable|integer',
            'scheduled_date'   => 'nullable|date',
            'started_at'       => 'nullable|date',
            'ended_at'         => 'nullable|date',
            'duration_minutes' => 'nullable|integer',
            'class_link'       => 'nullable|url',
            'record_link'      => 'nullable|url',
            'room_location'    => 'nullable|string|max:255',
            'source'           => 'nullable|in:online,offline',
            'status'           => 'nullable|in:0,1,2',
            'teacher_id'       => 'nullable|exists:teachers,id',
        ]);

        $this->classService->update($classId, $data);

        return response()->json([
            'success' => true,
            'message' => 'Class updated successfully.',
        ]);
    }

    // ── DELETE /my_courses/classes/{classId} ──────────────────────────────────

    public function destroyClass(int $classId)
    {
        $this->classService->delete($classId);

        return response()->json([
            'success' => true,
            'message' => 'Class deleted successfully.',
        ]);
    }

    // ── POST /my_courses/{courseId}/materials ─────────────────────────────────

    public function storeMaterial(Request $request, int $courseId)
    {
        $data = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'file_url'      => 'required|string',
            'material_type' => 'required|in:pdf,docx,mp3,wav,image,video,other',
            'order'         => 'nullable|integer',
            'status'        => 'nullable|in:0,1',
        ]);

        $data['course_id'] = $courseId;
        $material = $this->materialService->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Material added successfully.',
            'data'    => $material,
        ], 201);
    }

    // ── PUT /my_courses/materials/{materialId} ────────────────────────────────

    public function updateMaterial(Request $request, int $materialId)
    {
        $data = $request->validate([
            'title'         => 'sometimes|string|max:255',
            'description'   => 'nullable|string',
            'file_url'      => 'sometimes|string',
            'material_type' => 'sometimes|in:pdf,docx,mp3,wav,image,video,other',
            'order'         => 'nullable|integer',
            'status'        => 'nullable|in:0,1',
        ]);

        $this->materialService->update($materialId, $data);

        return response()->json([
            'success' => true,
            'message' => 'Material updated successfully.',
        ]);
    }

    // ── DELETE /my_courses/materials/{materialId} ─────────────────────────────

    public function destroyMaterial(int $materialId)
    {
        $this->materialService->delete($materialId);

        return response()->json([
            'success' => true,
            'message' => 'Material deleted successfully.',
        ]);
    }
}
