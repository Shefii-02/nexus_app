<?php

namespace App\Http\Controllers\API\Admin;

use App\DTOs\CourseDTO;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\ApiResponse;
use App\Http\Requests\CourseRequest;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Http\Resources\AdmissionStudentResource;
use App\Http\Resources\CourseResource;
use App\Http\Resources\TeacherResource;
use App\Models\Admission;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Course;
use App\Models\User;
use App\Services\Course\CourseService;
use Illuminate\Http\JsonResponse;
use App\Services\Media\MediaService;
use Beste\Json;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CourseController extends Controller
{
    use ApiResponse;

    public function __construct(
        private CourseService $courseService,
        private MediaService $mediaService
    ) {}


    public function index(): JsonResponse
    {
        $page = request()->query('page', 1);
        $perPage = request()->query('per_page', 15);


        $filters = [
            'search' => request('search'),
            'class_type' => request('mode'),
            'status' => request('status'),
        ];

        $courses = $this->courseService->list($page, $perPage, $filters);

        return $this->paginatedResponse(CourseResource::collection($courses), 'Courses retrieved successfully');
    }

    public function show(int $course): JsonResponse
    {
        $courseData = $this->courseService->findWithRelations($course, ['teacher']);

        if (!$courseData) {
            return $this->errorResponse('Course not found', null, 404);
        }

        return $this->successResponse(CourseResource::make($courseData), 'Course retrieved successfully');
    }

    public function store(CourseRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            // 🔥 handle file upload


            if ($request->hasFile('thumbnail')) {
                $media = $this->mediaService->upload($request->file('thumbnail'), auth()->id(), 'courses');
                $data['thumbnail'] = $media->id;
            }

            $dto = CourseDTO::fromArray($data);
            $course = $this->courseService->create($dto);

            $course->teachers()->syncWithoutDetaching([
                $request->teacher_id
            ]);


            return $this->successResponse(
                CourseResource::make($course->load(['teacher', 'batch'])),
                'Course created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create course', ['error' => $e->getMessage()], 500);
        }
    }

    public function update(CourseRequest $request, int $course): JsonResponse
    {
        try {

            if (!$this->courseService->exists($course)) {
                return $this->errorResponse('Course not found', null, 404);
            }

            $current = $this->courseService->find($course);

            $data = $request->validated();


            // if ($request->input('thumbnail') === null && $current->thumbnail) {
            //     $this->mediaService->delete($current->thumbnail);
            //     $data['thumbnail'] = null;
            // }

            if ($request->hasFile('thumbnail')) {

                // 1. delete old media
                if ($current->thumbnail && is_int($current->thumbnail)) {
                    $this->mediaService->delete($current->thumbnail);
                }
                $media = $this->mediaService->upload($request->file('thumbnail'), auth()->id(), 'courses');


                $data['thumbnail'] = $media->id;
            }


            $dto = CourseDTO::fromArray(array_merge(
                $current->toArray(),
                $data
            ));



            $this->courseService->update($course, $dto);
            $updated = $this->courseService->findWithRelations($course, ['teacher', 'batch']);



            if (
                $request->filled('teacher_id') &&
                !$updated->teachers()
                    ->where('users.id', $request->teacher_id)
                    ->exists()
            ) {

                $updated->teachers()->attach(
                    $request->teacher_id
                );
            }


            return $this->successResponse(CourseResource::make($updated), 'Course updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update course', ['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(int $course): JsonResponse
    {
        try {
            if (!$this->courseService->exists($course)) {
                return $this->errorResponse('Course not found', null, 404);
            }

            $current = $this->courseService->find($course);
            if ($current->thumbnail && is_int($current->thumbnail)) {
                $this->mediaService->delete($current->thumbnail);
            }

            $this->courseService->delete($course);

            return $this->successResponse(null, 'Course deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete course', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Attach student to course
     */
    public function attachStudent(int $course, int $student): JsonResponse
    {
        try {
            if (!$this->courseService->exists($course)) {
                return $this->errorResponse('Course not found', null, 404);
            }

            $this->courseService->attachStudent($course, $student);

            return $this->successResponse(null, 'Student attached to course successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to attach student', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Detach student from course
     */
    public function detachStudent(int $course, int $student): JsonResponse
    {
        try {
            if (!$this->courseService->exists($course)) {
                return $this->errorResponse('Course not found', null, 404);
            }

            $this->courseService->detachStudent($course, $student);

            return $this->successResponse(null, 'Student detached from course successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to detach student', ['error' => $e->getMessage()], 500);
        }
    }

    public function courseTeachers(int $course): JsonResponse
    {
        $teachers = User::query()
            ->where('acc_type', 'teacher')
            ->whereHas('courseTeachers', function ($q) use ($course) {
                $q->where('course_id', $course);
            })
            ->with('teacher')
            ->get();

        Log::info($teachers);

        return $this->successResponse(
            TeacherResource::collection($teachers),
            'Teachers retrieved successfully'
        );
    }
    public function addonTeachers(
        Request $request,
        int $course
    ): JsonResponse {

        $courseModel = Course::findOrFail($course);

        $search = $request->get('search');

        // teachers already assigned to course
        $assignedTeacherIds = $courseModel
            ->teachers()
            ->pluck('users.id')
            ->toArray();

        $teachers = User::query()
            ->where('acc_type', 'teacher')

            ->when($search, function ($query) use ($search) {

                $query->where(function ($q) use ($search) {

                    $q->where(
                        'name',
                        'like',
                        "%{$search}%"
                    )
                        ->orWhere(
                            'email',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'phone',
                            'like',
                            "%{$search}%"
                        );
                });
            })
            ->orderBy('name')
            ->get()

            ->map(function ($teacher) use ($assignedTeacherIds) {
                return [
                    'id' => $teacher->id,
                    'name' => $teacher->name,
                    'email' => $teacher->email,
                    'phone' => $teacher->phone,

                    'checked' => in_array(
                        $teacher->id,
                        $assignedTeacherIds
                    ),
                ];
            });

        return $this->successResponse([
            'course_id' => $courseModel->id,
            'course_title' => $courseModel->name,
            'teachers' => $teachers,
        ]);
    }

    public function addonTeachersStore(
        Request $request,
        int $course
    ): JsonResponse {

        $request->validate([
            'teacher_ids' => 'array',
            'teacher_ids.*' => 'exists:users,id',
        ]);

        $courseModel = Course::findOrFail($course);

        $teacherIds = $request->teacher_ids ?? [];

        // keep pivot table synced
        $courseModel->teachers()->sync(
            $teacherIds
        );

        // main teacher removed
        if (
            $courseModel->teacher_id &&
            !in_array(
                $courseModel->teacher_id,
                $teacherIds
            )
        ) {

            $courseModel->teacher_id = count($teacherIds)
                ? $teacherIds[0]
                : null;

            $courseModel->save();
        }

        return $this->successResponse(
            null,
            'Teachers assigned successfully'
        );
    }




    public function students(int $course): JsonResponse
    {
        $students = Admission::query()
            ->where('course_id', $course)
            // ->where('status', 'active')
            ->with([
                'student',
                'course'
            ])
            ->latest()
            ->paginate(20);

        return $this->paginatedResponse(
            AdmissionStudentResource::collection($students),
            'Students retrieved successfully'
        );
    }

    public function bulkUpdateStudents(
        Request $request,
        int $course
    ): JsonResponse {

        $request->validate([
            'admission_ids' => 'required|array',
            'status' => 'nullable|string',
            'expiry_date' => 'nullable|date',
        ]);

        Admission::where(
            'course_id',
            $course
        )
            ->whereIn(
                'id',
                $request->admission_ids
            )
            ->update([
                'status' => $request->status,
                'expiry_date' => $request->expiry_date,
            ]);

        return $this->successResponse(
            null,
            'Students updated successfully'
        );
    }

    public function removeStudent(
        int $course,
        int $admission
    ): JsonResponse {

        $record = Admission::where(
            'course_id',
            $course
        )->findOrFail($admission);

        $record->delete();

        return $this->successResponse(
            null,
            'Student removed successfully'
        );
    }

    public function updateStudent(
        Request $request,
        int $course,
        int $admission
    ): JsonResponse {

        $request->validate([
            'status' => 'required|string',
            'expiry_date' => 'required|date',
        ]);

        $record = Admission::where(
            'course_id',
            $course
        )->findOrFail($admission);

        $record->update([
            'status' => $request->status,
            'expiry_date' => $request->expiry_date,
        ]);

        return $this->successResponse(
            null,
            'Student updated successfully'
        );
    }

    public function conversation(int $course)
    {
        $courseModel = Course::findOrFail($course);

        $conversation = Conversation::with('mParticipants')->where(
            'module_id',
            $course
        )
            ->where('type', 'group')
            ->first();

        if (!$conversation) {

            return $this->successResponse([
                'id' => null,
                'title' => $courseModel->name . ' Group',
                'avatar' => null,
                'status' => 'active',
                'is_new' => true,
                'participants' => collect()
            ]);
        }

        return $this->successResponse([
            'id' => $conversation->id,
            'title' => $conversation->title,
            'avatar' => $conversation->avatar_url,
            'status' => $conversation->status,
            'is_new' => false,
            'participants' => $conversation->mParticipants
        ]);
    }

    public function participants(
        Request $request,
        int $course
    ) {
        $teacherIds =
            DB::table('teachers_courses')
            ->where('course_id', $course)
            ->pluck('teacher_id')
            ->toArray();

        $studentIds =
            Admission::where(
                'course_id',
                $course
            )
            ->where(
                'status',
                'active'
            )
            ->pluck('student_id')
            ->toArray();

        $ids = array_merge(
            $teacherIds,
            $studentIds
        );

        $conversation =
            Conversation::where(
                'module_id',
                $course
            )
            ->where(
                'type',
                'group'
            )
            ->first();

        $joinedIds = $conversation
            ? $conversation
            ->participants()
            ->pluck('users.id')
            ->toArray()
            : [];

        $users = User::whereIn(
            'id',
            $ids
        )
            ->get()
            ->map(function ($user)
            use ($joinedIds) {

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'checked' =>
                    in_array(
                        $user->id,
                        $joinedIds
                    ),
                ];
            });

        return $this->successResponse(
            $users
        );
    }


    public function conversationMembers(
        int $course
    ): JsonResponse {

        $conversation = Conversation::query()

            ->where('type', 'group')
            ->where('module_id', $course)

            ->first();

        if (!$conversation) {

            return $this->successResponse([]);
        }

        $users = User::query()

            ->whereIn(
                'id',
                $conversation
                    ->participants()
                    ->pluck('user_id')
            )

            ->get()

            ->map(function ($user) {

                return [

                    'id' => $user->id,

                    'name' => $user->name,

                    'email' => $user->email,

                    'phone' => $user->phone,

                    'avatar' => $user->avatar_url,

                    'acc_type' => $user->acc_type,
                ];
            });

        return $this->successResponse(
            $users
        );
    }


    public function conversationUsers(
        Request $request,
        int $course
    ): JsonResponse {

        $search = $request->search;

        $conversation = Conversation::query()

            ->where('type', 'group')
            ->where('module_id', $course)

            ->first();

        $existingIds = [];

        if ($conversation) {

            $existingIds = $conversation
                ->participants()
                ->pluck('user_id')
                ->toArray();
        }

        $users = User::query()

            ->when(
                $search,
                function ($query) use ($search) {

                    $query->where(function ($q) use ($search) {

                        $q->where(
                            'name',
                            'like',
                            "%{$search}%"
                        )
                            ->orWhere(
                                'email',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'phone',
                                'like',
                                "%{$search}%"
                            );
                    });
                }
            )

            ->limit(50)

            ->get()

            ->map(function ($user) use ($existingIds) {

                return [

                    'id' => $user->id,

                    'name' => $user->name,

                    'email' => $user->email,

                    'phone' => $user->phone,

                    'avatar' => $user->avatar_url,

                    'acc_type' => $user->acc_type,

                    'checked' => in_array(
                        $user->id,
                        $existingIds
                    ),
                ];
            });

        return $this->successResponse(
            $users
        );
    }


    public function saveConversation(
        Request $request,
        int $course
    ): JsonResponse {

        $request->validate([

            'title' => 'required',

            'status' => 'required',

            'participant_ids' => 'array',
        ]);

        $conversation = Conversation::firstOrNew([

            'type' => 'group',

            'module_id' => $course,
        ]);

        $conversation->title =
            $request->title;

        $conversation->status =
            $request->status;

        $conversation->created_by =
            auth()->id();

        if ($request->hasFile('avatar')) {

            $media = $this->mediaService
                ->upload(
                    $request->file('avatar'),
                    auth()->id(),
                    'conversation'
                );

            $conversation->avatar =
                $media->id;
        }

        $conversation->save();

        $participantIds =
            $request->participant_ids ?? [];

        foreach (
            $participantIds
            as $userId
        ) {

            ConversationParticipant::firstOrCreate([

                'conversation_id' =>
                $conversation->id,

                'user_id' =>
                $userId,
            ]);
        }

        return $this->successResponse(
            $conversation,
            'Conversation saved successfully'
        );
    }


    public function removeConversationMember(
        int $course,
        int $user
    ): JsonResponse {

        $conversation = Conversation::query()

            ->where('type', 'group')
            ->where('module_id', $course)

            ->firstOrFail();

        ConversationParticipant::query()

            ->where(
                'conversation_id',
                $conversation->id
            )

            ->where(
                'user_id',
                $user
            )

            ->delete();

        return $this->successResponse(
            null,
            'Member removed successfully'
        );
    }


    public function chatList(Request $request)
    {
        // Return only courses the authenticated user is enrolled in or teaches
        $user   = $request->user();
        $search = trim(
            $request->get('q')
                ?? $request->get('search')
                ?? ''
        );
        $courses = \App\Models\Course::where('name',   'like', "%{$search}%")
            // ->where(function ($q) use ($user) {
            //     $q->where('teacher_id', $user->id)
            //         ->orWhereHas('enrollments', fn($e) => $e->where('user_id', $user->id));
            // })

            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json(['data' => $courses]);
    }
}
