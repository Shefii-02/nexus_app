<?php

namespace App\Http\Controllers\API\Admin;

use App\DTOs\StaffDTO;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\ApiResponse;
use App\Http\Requests\StoreStaffRequest;
use App\Http\Requests\UpdateStaffRequest;
use App\Http\Resources\StaffResource;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\User;
use App\Models\UserAppPermission;
use App\Services\Staff\StaffService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class StaffController extends Controller
{
    use ApiResponse;

    public function __construct(private StaffService $staffService) {}

    public function index(): JsonResponse
    {
        $page = request()->query('page', 1);
        $perPage = request()->query('per_page', 1500);
        $filters = request()->query('filters', []);

        $staff = $this->staffService->list($page, $perPage, $filters);

        return $this->paginatedResponse(
            StaffResource::collection($staff),
            'Staff retrieved successfully'
        );
    }

    public function show(int $staff): JsonResponse
    {
        $staffData = $this->staffService->findWithRelations($staff, ['staff']);

        if (!$staffData) {
            return $this->errorResponse('Staff member not found', null, 404);
        }

        return $this->successResponse(StaffResource::make($staffData), 'Staff retrieved successfully');
    }

    public function store(StoreStaffRequest $request): JsonResponse
    {
        try {

            $dto = StaffDTO::fromArray(
                $request->validated()
            );

            $staff = $this->staffService->create($dto);

            /*
        |--------------------------------------------------------------------------
        | Sync Permissions
        |--------------------------------------------------------------------------
        */

            $validated = $request->validate([
                'permissions' => ['nullable', 'array'],
                'permissions.*' => ['boolean'],
            ]);

            if (isset($validated['permissions'])) {

                $this->syncPermissions(
                    $staff->user_id,
                    $validated['permissions']
                );
            }



            /* |--------------------------------------------------------------------------
        | Create Direct Chat with First Super Admin
        |--------------------------------------------------------------------------
        */
            // $admin = User::where('acc_type', 'admin')
            //     ->where('status', 1)
            //     ->orderBy('id')
            //     ->first();

            // if ($admin && $admin->id != $staff->id) {

            //     $conversation = Conversation::where('type', 'single')
            //         ->whereHas('participants', function ($q) use ($staff) {
            //             $q->where('user_id', $staff->id);
            //         })
            //         ->whereHas('participants', function ($q) use ($admin) {
            //             $q->where('user_id', $admin->id);
            //         })
            //         ->withCount('participants')
            //         ->having('participants_count', 2)
            //         ->first();

            //     if (!$conversation) {

            //         DB::transaction(function () use ($admin, $staff) {

            //             $conversation = Conversation::create([
            //                 'type'       => 'single',
            //                 'title'      => null,
            //                 'created_by' => $admin->id,
            //                 'status'     => "active",
            //             ]);
            //             if ($conversation) {
            //                 ConversationParticipant::create([
            //                     'conversation_id' => $conversation->id,
            //                     'user_id'         => $admin->id,
            //                     'created_by'      => $admin->id,
            //                     'status'          => "active",
            //                 ]);

            //                 ConversationParticipant::create([
            //                     'conversation_id' => $conversation->id,
            //                     'user_id'         => $staff->id,
            //                     'created_by'      => $admin->id,
            //                     'status'          => "active",
            //                 ]);
            //             }
            //         });
            //     }
            // }
            $admin = User::where('acc_type', 'admin')
                ->where('status', 1)
                ->orderBy('id')
                ->first();

            if ($admin && $admin->id != $staff->user_id) {

                $conversation = Conversation::where('type', 'single')
                    ->whereHas('participants', function ($q) use ($staff) {
                        $q->where('user_id', $staff->user_id);
                    })
                    ->whereHas('participants', function ($q) use ($admin) {
                        $q->where('user_id', $admin->id);
                    })
                    ->withCount('participants')
                    ->having('participants_count', 2)
                    ->first();

                if (!$conversation) {

                    DB::transaction(function () use ($admin, $staff) {

                        $conversation = Conversation::create([
                            'type'       => 'single',
                            'title'      => null,
                            'created_by' => $admin->id,
                            'status'     => 'active',
                        ]);

                        ConversationParticipant::create([
                            'conversation_id' => $conversation->id,
                            'user_id'         => $admin->id,
                            'created_by'      => $admin->id,
                            'status'           => 'active',
                        ]);

                        ConversationParticipant::create([
                            'conversation_id' => $conversation->id,
                            'user_id'         => $staff->user_id,
                            'created_by'      => $admin->id,
                            'status'           => 'active',
                        ]);
                    });
                }
            }


            return $this->successResponse(
                StaffResource::make(
                    $staff->load('user')
                ),
                'Staff created successfully',
                201
            );
        } catch (\Throwable $e) {

            Log::error('Failed to create staff', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->errorResponse(
                'Failed to create staff',
                [
                    'error' => $e->getMessage(),
                ],
                500
            );
        }
    }

    public function update(
        UpdateStaffRequest $request,
        int $staff
    ): JsonResponse {
        try {

            if (!$this->staffService->exists($staff)) {
                return $this->errorResponse(
                    'Staff member not found',
                    null,
                    404
                );
            }

            $current = $this->staffService->find($staff);

            $dto = StaffDTO::fromArray(
                array_merge(
                    $current->toArray(),
                    $request->validated()
                )
            );

            $this->staffService->update(
                $staff,
                $dto
            );

            /*
        |--------------------------------------------------------------------------
        | Sync Permissions
        |--------------------------------------------------------------------------
        */

            $validated = $request->validate([
                'permissions' => ['nullable', 'array'],
                'permissions.*' => ['boolean'],
            ]);

            if (isset($validated['permissions'])) {

                // Here $staff is the User ID based on your current code
                $this->syncPermissions(
                    $staff,
                    $validated['permissions']
                );
            }

            $updated = $this->staffService->findWithRelations(
                $staff,
                ['staff']
            );

            return $this->successResponse(
                StaffResource::make($updated),
                'Staff updated successfully'
            );
        } catch (\Throwable $e) {

            Log::error('Failed to update staff', [
                'staff_id' => $staff,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->errorResponse(
                'Failed to update staff',
                [
                    'error' => $e->getMessage(),
                ],
                500
            );
        }
    }

    public function destroy(Request $request, int $staff): JsonResponse
    {
        try {
            if (!$this->staffService->exists($staff)) {
                return $this->errorResponse('Staff member not found', null, 404);
            }
            $user = $request->user();

            // if ($user->acc_type === 'admin') {
            //     $this->staffService->forceDelete($staff);
            // } else {
            // $this->staffService->delete($staff);
            // }
            // $this->staffService->delete($staff);

            $this->deleteUserData($user);
            $user->forceDelete(); // ← permanent delete, bypasses SoftDeletes


            return $this->successResponse(null, 'Staff deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete staff', ['error' => $e->getMessage()], 500);
        }
    }

    public function permissionUpdate(Request $request, int $userId): JsonResponse
    {
        $validated = $request->validate([
            'permissions' => ['required', 'array'],
            'permissions.*' => ['boolean'],
        ]);

        try {

            DB::transaction(function () use ($validated, $userId) {

                $permissions = collect($validated['permissions'])
                    ->only(UserAppPermission::KEYS);

                /*
            |--------------------------------------------------------------------------
            | Delete old permissions that are not in the request
            |--------------------------------------------------------------------------
            */

                UserAppPermission::where('user_id', $userId)
                    ->whereNotIn(
                        'permission_key',
                        $permissions->keys()->toArray()
                    )
                    ->delete();

                /*
            |--------------------------------------------------------------------------
            | Insert / Update all submitted permissions
            |--------------------------------------------------------------------------
            */

                foreach ($permissions as $key => $granted) {

                    UserAppPermission::updateOrCreate(
                        [
                            'user_id' => $userId,
                            'permission_key' => $key,
                        ],
                        [
                            'granted' => (bool) $granted,
                        ]
                    );
                }
            });

            $user = User::with('appPermissions')
                ->findOrFail($userId);

            return $this->successResponse(
                $user,
                'Permissions updated successfully'
            );
        } catch (\Throwable $e) {

            Log::error('Permission update failed', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse(
                'Failed to update permissions',
                [
                    'error' => $e->getMessage(),
                ],
                500
            );
        }
    }


    private function syncPermissions(int $userId, array $permissions): void
    {
        $permissions = collect($permissions)
            ->only(UserAppPermission::KEYS);

        // Delete old permissions that are not included in the request
        UserAppPermission::where('user_id', $userId)
            ->whereNotIn(
                'permission_key',
                $permissions->keys()->toArray()
            )
            ->delete();

        // Insert or update all submitted permissions
        foreach ($permissions as $key => $granted) {
            UserAppPermission::updateOrCreate(
                [
                    'user_id' => $userId,
                    'permission_key' => $key,
                ],
                [
                    'granted' => (bool) $granted,
                ]
            );
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
