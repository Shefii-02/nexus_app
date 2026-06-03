<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\API\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $page = request()->query('page', 1);
        $perPage = request()->query('per_page', 15);

        $roles = Role::with('permissions')->paginate($perPage, ['*'], 'page', $page);

        return $this->paginatedResponse(RoleResource::collection($roles), 'Roles retrieved successfully');
    }

    public function show(int $role): JsonResponse
    {
        $roleData = Role::with('permissions')->find($role);

        if (!$roleData) {
            return $this->errorResponse('Role not found', null, 404);
        }

        return $this->successResponse(RoleResource::make($roleData), 'Role retrieved successfully');
    }

    public function store(RoleRequest $request): JsonResponse
    {
        $role = Role::create(array_merge($request->validated(), ['guard_name' => 'api']));
        $role->syncPermissions($request->permissions);
        $role->load('permissions');
        return $this->successResponse(RoleResource::make($role), 'Role created successfully', 201);
    }

    public function update(RoleRequest $request, int $role): JsonResponse
    {
        $roleData = Role::find($role);

        if (!$roleData) {
            return $this->errorResponse('Role not found', null, 404);
        }

        $validated = $request->validated();
        $roleData->update($validated);
        $roleData->syncPermissions($request->permissions);
        $roleData->load('permissions');
        return $this->successResponse(RoleResource::make($roleData), 'Role updated successfully');
    }

    public function destroy(int $role): JsonResponse
    {
        $roleData = Role::find($role);

        if (!$roleData) {
            return $this->errorResponse('Role not found', null, 404);
        }

        $roleData->delete();

        return $this->successResponse(null, 'Role deleted successfully');
    }


    public function permissions(): JsonResponse
    {
        $permissions = \Spatie\Permission\Models\Permission::all();

        return $this->successResponse(PermissionResource::collection($permissions), 'Permissions retrieved successfully');
    }
}
