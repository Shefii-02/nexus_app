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

            return $this->successResponse(StaffResource::make($updated), 'Staff updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update staff', ['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(int $staff): JsonResponse
    {
        try {
            if (!$this->staffService->exists($staff)) {
                return $this->errorResponse('Staff member not found', null, 404);
            }

            $this->staffService->delete($staff);

            return $this->successResponse(null, 'Staff deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete staff', ['error' => $e->getMessage()], 500);
        }
    }


    public function permissionUpdate(Request $request, User $user)
    {
        $validated = $request->validate([
            'permissions' => ['required', 'array'],
            'permissions.*' => ['boolean'],
        ]);

        $rows = collect($validated['permissions'])
            ->only(UserAppPermission::KEYS)
            ->map(fn($granted, $key) => [
                'user_id' => $user->id,
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

        return response()->json([
            'status' => true,
            'message' => 'Permissions updated successfully',
            'data' => $user->fresh()->permissions_map,
        ]);
    }
}
