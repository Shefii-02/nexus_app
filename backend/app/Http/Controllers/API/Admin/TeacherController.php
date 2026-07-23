<?php

namespace App\Http\Controllers\API\Admin;

use App\DTOs\TeacherDTO;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\ApiResponse;
use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use App\Http\Resources\TeacherResource;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\User;
use App\Services\Teacher\TeacherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TeacherController extends Controller
{
    use ApiResponse;

    public function __construct(private TeacherService $teacherService) {}

    /**
     * Get all teachers with pagination
     */
    public function index(): JsonResponse
    {
        $page = request()->query('page', 1);
        $perPage = request()->query('per_page', 15);


        $filters = [
            'search' => request('search'),
            'status' => request('status'),
            'acc_type' => 'teacher',
        ];

        $teachers = $this->teacherService->list(
            $page,
            $perPage,
            $filters
        );


        return $this->paginatedResponse(
            TeacherResource::collection($teachers),
            'Teachers retrieved successfully'
        );
    }

    /**
     * Get single teacher
     */
    public function show(int $user): JsonResponse
    {
        $teacherData = $this->teacherService->findWithRelations($user, ['teacher', 'courses']);

        if (!$teacherData) {
            return $this->errorResponse('Teacher not found', null, 404);
        }

        return $this->successResponse(TeacherResource::make($teacherData), 'Teacher retrieved successfully');
    }

    /**
     * Create new teacher
     */
    public function store(StoreTeacherRequest $request)
    {
        try {
            $dto = TeacherDTO::fromArray($request->validated());

            $teacher = $this->teacherService->create($dto);


            /* |--------------------------------------------------------------------------
        | Create Direct Chat with First Super Admin
        |--------------------------------------------------------------------------
        */
            $admin = User::where('acc_type', 'admin')
                ->where('status', 1)
                ->orderBy('id')
                ->first();

            if ($admin && $admin->id != $teacher->id) {

                $conversation = Conversation::where('type', 'single')
                    ->whereHas('participants', function ($q) use ($teacher) {
                        $q->where('user_id', $teacher->id);
                    })
                    ->whereHas('participants', function ($q) use ($admin) {
                        $q->where('user_id', $admin->id);
                    })
                    ->withCount('participants')
                    ->having('participants_count', 2)
                    ->first();

                if (!$conversation) {

                    DB::transaction(function () use ($admin, $teacher) {

                        $conversation = Conversation::create([
                            'type'       => 'single',
                            'title'      => null,
                            'created_by' => $admin->id,
                            'status'     => "active",
                        ]);

                        ConversationParticipant::create([
                            'conversation_id' => $conversation->id,
                            'user_id'         => $admin->id,
                            'created_by'      => $admin->id,
                            'status'          => "active",
                        ]);

                        ConversationParticipant::create([
                            'conversation_id' => $conversation->id,
                            'user_id'         => $teacher->id,
                            'created_by'      => $admin->id,
                            'status'          => "active",
                        ]);
                    });
                }
            }


            return $this->successResponse(
                TeacherResource::make($teacher->load('teacher')),
                'Teacher created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create teacher', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update teacher
     */
    public function update(UpdateTeacherRequest $request, int $teacher): JsonResponse
    {
        try {
            $existing = $this->teacherService->find($teacher);

            if (!$existing) {
                return $this->errorResponse('Teacher not found', null, 404);
            }

            // ✅ Only use validated request
            $dto = TeacherDTO::fromArray($request->validated());

            // ✅ Pass existing model if needed
            $updated = $this->teacherService->update($teacher, $dto);

            return $this->successResponse(
                TeacherResource::make($updated->load('teacher')),
                'Teacher updated successfully'
            );
        } catch (\Exception $e) {
            // Log::info($e->getMessage());
            return $this->errorResponse(
                'Failed to update teacher',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Delete teacher
     */
    public function destroy(Request $request, int $teacher): JsonResponse
    {
        try {
            if (!$this->teacherService->exists($teacher)) {
                return $this->errorResponse('Teacher not found', null, 404);
            }
            $user = $request->user();

            // if ($user->acc_type === 'admin') {
            //     $this->teacherService->forceDelete($teacher);
            // } else {
            $this->teacherService->delete($teacher);
            // }
            // $this->teacherService->delete($teacher);

            return $this->successResponse(null, 'Teacher deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete teacher', ['error' => $e->getMessage()], 500);
        }
    }
}
