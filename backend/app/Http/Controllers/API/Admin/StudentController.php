<?php

namespace App\Http\Controllers\API\Admin;

use App\DTOs\StudentDTO;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\ApiResponse;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Http\Resources\StudentResource;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\User;
use App\Services\Student\StudentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentController extends Controller
{
    use ApiResponse;

    public function __construct(private StudentService $studentService) {}

    public function index(Request $request): JsonResponse
    {
        $page = $request->query('page', 1);
        $perPage = $request->query('per_page', 15);
        // $filters = $request->query('filters', []);

        $filters = [
            'search' => request('search'),
            'status' => request('status'),
            'acc_type' => 'student',
        ];


        $students = $this->studentService->list($page, $perPage, $filters);

        return $this->paginatedResponse(
            StudentResource::collection($students),
            'Students retrieved successfully'
        );
    }

    public function show(int $student): JsonResponse
    {
        $studentData = $this->studentService->findWithRelations($student, ['student']);

        if (!$studentData) {
            return $this->errorResponse('Student not found', null, 404);
        }

        return $this->successResponse(StudentResource::make($studentData->load('student')), 'Student retrieved successfully');
    }

    public function store(StoreStudentRequest $request): JsonResponse
    {

        try {
            $dto = StudentDTO::fromArray($request->validated());
            $student = $this->studentService->create($dto);


            /* |--------------------------------------------------------------------------
        | Create Direct Chat with First Super Admin
        |--------------------------------------------------------------------------
        */
            $admin = User::where('acc_type', 'admin')
                ->where('status', 1)
                ->orderBy('id')
                ->first();

            if ($admin && $admin->id != $student->id) {

                $conversation = Conversation::where('type', 'single')
                    ->whereHas('participants', function ($q) use ($student) {
                        $q->where('user_id', $student->id);
                    })
                    ->whereHas('participants', function ($q) use ($admin) {
                        $q->where('user_id', $admin->id);
                    })
                    ->withCount('participants')
                    ->having('participants_count', 2)
                    ->first();

                if (!$conversation) {

                    DB::transaction(function () use ($admin, $student) {

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
                            'user_id'         => $student->id,
                            'created_by'      => $admin->id,
                            'status'          => "active",
                        ]);
                    });
                }
            }


            return $this->successResponse(
                StudentResource::make($student->load('user')),
                'Student created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create student', ['error' => $e->getMessage()], 500);
        }
    }

    public function update(UpdateStudentRequest $request, int $student): JsonResponse
    {
        try {

            if (!$this->studentService->exists($student)) {
                return $this->errorResponse('Student not found', null, 404);
            }

            // ✅ Only use validated request

            $dto = StudentDTO::fromArray($request->validated());

            // ✅ Pass existing model if needed
            $updated = $this->studentService->update($student, $dto);

            return $this->successResponse(
                StudentResource::make($updated->load('student')),
                'Student updated successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update student', ['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request, int $student): JsonResponse
    {
        Log::info($request->student);
        try {
            if (!$this->studentService->exists($student)) {
                return $this->errorResponse('Student not found', null, 404);
            }

            $user = $request->user();

            // if ($user->acc_type === 'admin') {
            //     $this->studentService->forceDelete($student);
            // } else {
                $this->studentService->delete($student);
            // }
            // $this->studentService->delete($student);

            return $this->successResponse(null, 'Student deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete student', ['error' => $e->getMessage()], 500);
        }
    }
}
