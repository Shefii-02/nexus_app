<?php

namespace App\Http\Controllers\API\Admin;

use App\DTOs\StaffDTO;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\ApiResponse;
use App\Http\Requests\StoreStaffRequest;
use App\Http\Requests\UpdateStaffRequest;
use App\Http\Resources\StaffResource;
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
            $dto = StaffDTO::fromArray($request->validated());
            $staff = $this->staffService->create($dto);

            $validated = $request->validate([
                'permissions' => ['nullable', 'array'],
                'permissions.*' => ['boolean'],
            ]);

            if (!empty($validated['permissions'])) {
                // NOTE: confirm this matches your schema.
                // permissionUpdate() below keys permissions to the User's id
                // ($user->id), so this uses $staff->user_id to stay consistent
                // (assumes $staffService->create() returns a Staff model with
                // a user_id FK, per the $staff->load('user') call further down).
                // If create() instead returns the User model directly, change
                // this to $staff->id.
                $rows = collect($validated['permissions'])
                    ->only(UserAppPermission::KEYS)
                    ->map(fn($granted, $key) => [
                        'user_id' => $staff->user_id,
                        'permission_key' => $key,
                        'granted' => (bool) $granted,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ])
                    ->values()
                    ->all();

                DB::transaction(function () use ($rows) {
                    foreach ($rows as $row) {
                        UserAppPermission::updateOrCreate(
                            ['user_id' => $row['user_id'], 'permission_key' => $row['permission_key']],
                            ['granted' => $row['granted']],
                        );
                    }
                });
            }

            return $this->successResponse(
                StaffResource::make($staff->load('user')),
                'Staff created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create staff', ['error' => $e->getMessage()], 500);
        }
    }

    public function update(UpdateStaffRequest $request, int $staff): JsonResponse
    {
        try {
            if (!$this->staffService->exists($staff)) {
                return $this->errorResponse('Staff member not found', null, 404);
            }

            $current = $this->staffService->find($staff);
            $dto = StaffDTO::fromArray(array_merge(
                $current->toArray(),
                $request->validated()
            ));

            $this->staffService->update($staff, $dto);
            $updated = $this->staffService->findWithRelations($staff, ['staff']);

            $validated = $request->validate([
                'permissions' => ['nullable', 'array'],
                'permissions.*' => ['boolean'],
            ]);

            if (!empty($validated['permissions'])) {
                $rows = collect($validated['permissions'])
                    ->only(UserAppPermission::KEYS)
                    ->map(fn($granted, $key) => [
                        'user_id' => $staff, // here $staff is the int route param, so this is already correct
                        'permission_key' => $key,
                        'granted' => (bool) $granted,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ])
                    ->values()
                    ->all();

                DB::transaction(function () use ($rows) {
                    foreach ($rows as $row) {
                        UserAppPermission::updateOrCreate(
                            ['user_id' => $row['user_id'], 'permission_key' => $row['permission_key']],
                            ['granted' => $row['granted']],
                        );
                    }
                });
            }

            return $this->successResponse(StaffResource::make($updated), 'Staff updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update staff', ['error' => $e->getMessage()], 500);
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

    public function permissionUpdate(Request $request, int $user)
    {
        Log::info($request->all());
        $validated = $request->validate([
            'permissions' => ['required', 'array'],
            'permissions.*' => ['boolean'],
        ]);

        $rows = collect($validated['permissions'])
            ->only(UserAppPermission::KEYS)
            ->map(fn($granted, $key) => [
                'user_id' => $user,
                'permission_key' => $key,
                'granted' => (bool) $granted,
                'updated_at' => now(),
                'created_at' => now(),
            ])
            ->values()
            ->all();

        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                UserAppPermission::updateOrCreate(
                    ['user_id' => $row['user_id'], 'permission_key' => $row['permission_key']],
                    ['granted' => $row['granted']],
                );
            }
        });

        $user = User::where('id', $user)->first();

        return response()->json([
            'status' => true,
            'message' => 'Permissions updated successfully',
            'data' => $user,
        ]);
    }
}
