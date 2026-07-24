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

class StaffController extends Controller
{
    use ApiResponse;

    public function __construct(private StaffService $staffService) {}

    public function index(): JsonResponse
    {
        $page = request()->query('page', 1);
        $perPage = request()->query('per_page', 15);
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
            $admin = User::where('acc_type', 'admin')
                ->where('status', 1)
                ->orderBy('id')
                ->first();

            if ($admin && $admin->id != $staff->id) {

                $conversation = Conversation::where('type', 'single')
                    ->whereHas('participants', function ($q) use ($staff) {
                        $q->where('user_id', $staff->id);
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
                            'status'     => "active",
                        ]);
                        if ($conversation) {
                            ConversationParticipant::create([
                                'conversation_id' => $conversation->id,
                                'user_id'         => $admin->id,
                                'created_by'      => $admin->id,
                                'status'          => "active",
                            ]);

                            ConversationParticipant::create([
                                'conversation_id' => $conversation->id,
                                'user_id'         => $staff->id,
                                'created_by'      => $admin->id,
                                'status'          => "active",
                            ]);
                        }
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
            $this->staffService->delete($staff);
            // }
            // $this->staffService->delete($staff);

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
}
