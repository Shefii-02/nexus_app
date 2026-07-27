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
use Illuminate\Support\Facades\Storage;

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
        $perPage = request()->query('per_page', 1500);


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


            $teacherUser =  User::findOrFail($teacher);
            $this->deleteUserData($teacherUser);

            // $this->deleteUserData($user);
            // $user->forceDelete(); // ← permanent delete, bypasses SoftDeletes


            // if ($user->acc_type === 'admin') {
            //     $this->teacherService->forceDelete($teacher);
            // } else {
            // $this->teacherService->delete($teacher);
            // }
            // $this->teacherService->delete($teacher);

            return $this->successResponse(null, 'Teacher deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete teacher', ['error' => $e->getMessage()], 500);
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
