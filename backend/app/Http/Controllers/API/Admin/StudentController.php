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
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    use ApiResponse;

    public function __construct(private StudentService $studentService) {}

    public function index(Request $request): JsonResponse
    {
        $page = $request->query('page', 1);
        $perPage = $request->query('per_page', 1500);
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
        // Log::info($request->student);
        try {
            if (!$this->studentService->exists($student)) {
                return $this->errorResponse('Student not found', null, 404);
            }

            $user = $request->user();

            // if ($user->acc_type === 'admin') {
            //     $this->studentService->forceDelete($student);
            // } else {
            // $this->studentService->delete($student);
            // }
            // $this->studentService->delete($student);


            $studentUser =  User::findOrFail($student);
            $this->deleteUserData($studentUser);

            // $user->forceDelete(); // ← permanent delete, bypasses SoftDeletes


            return $this->successResponse(null, 'Student deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete student', ['error' => $e->getMessage()], 500);
        }
    }

    private function deleteUserData(User $user): void
    {
        DB::table('activity_logs')->where('user_id', $user->id)->delete();
        DB::table('announcement_user')->where('user_id', $user->id)->delete();
        DB::table('app_notifications')->where('user_id', $user->id)->delete();
        DB::table('calls')->where('teacher_id', $user->id)->delete();
        DB::table('calls')->where('student_id', $user->id)->delete();
        DB::table('call_recipients')->where('student_id', $user->id)->delete();
        DB::table('conversation_participants')->where('user_id', $user->id)->delete();

        // ── Messages: must clean up dependents BEFORE deleting the messages ──
        $messageIds = DB::table('messages')->where('sender_id', $user->id)->pluck('id');

        if ($messageIds->isNotEmpty()) {
            DB::table('message_reactions')->whereIn('message_id', $messageIds)->delete();
            DB::table('message_reads')->whereIn('message_id', $messageIds)->delete();

            // Polls attached to this user's messages
            $pollIds = DB::table('polls')->whereIn('message_id', $messageIds)->pluck('id');
            if ($pollIds->isNotEmpty()) {
                DB::table('poll_votes')->whereIn('poll_id', $pollIds)->delete();
                DB::table('poll_options')->whereIn('poll_id', $pollIds)->delete();
                DB::table('polls')->whereIn('id', $pollIds)->delete();
            }

            // Other messages replying to this user's messages — don't cascade-delete
            // those messages, just detach the reply link.
            DB::table('messages')->whereIn('reply_to', $messageIds)->update(['reply_to' => null]);
        }

        DB::table('messages')->where('sender_id', $user->id)->delete();
        DB::table('message_reactions')->where('user_id', $user->id)->delete(); // reactions THIS user made elsewhere
        DB::table('message_reads')->where('user_id', $user->id)->delete();
        DB::table('poll_votes')->where('user_id', $user->id)->delete(); // votes THIS user cast elsewhere

        DB::table('refresh_tokens')->where('user_id', $user->id)->delete();
        DB::table('staff')->where('user_id', $user->id)->delete();
        DB::table('staff_payments')->where('staff_id', $user->id)->delete();
        DB::table('students')->where('user_id', $user->id)->delete();
        DB::table('teachers')->where('user_id', $user->id)->delete();
        DB::table('teachers_courses')->where('teacher_id', $user->id)->delete();
        DB::table('teacher_payments')->where('teacher_id', $user->id)->delete();
        DB::table('teacher_payment_items')->where('teacher_id', $user->id)->delete();
        DB::table('user_app_permissions')->where('user_id', $user->id)->delete();
        DB::table('user_devices')->where('user_id', $user->id)->delete();
        DB::table('user_platforms')->where('user_id', $user->id)->delete();

        // ── Courses / conversations this user "owns" — DECIDE how to handle ──
        // (see explanation above — reassign, block, or cascade; nulling shown
        // here only works if these columns are nullable in your schema)
        DB::table('courses')->where('teacher_id', $user->id)->update(['teacher_id' => null]);
        DB::table('conversations')->where('created_by', $user->id)->update(['created_by' => 1]);

        // ── Media files: delete storage files AND their DB rows ──────────────
        $mediaFiles = DB::table('media_files')->where('user_id', $user->id)->get();
        foreach ($mediaFiles as $mediaFile) {
            if ($mediaFile->file_path && Storage::exists($mediaFile->file_path)) {
                Storage::delete($mediaFile->file_path);
            }
        }
        DB::table('media_files')->where('user_id', $user->id)->delete();
    }
}
