<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\API\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $page = request()->query('page', 1);
        $perPage = request()->query('per_page', 15);
        $filters = request()->query('filters', []);

        $query = Group::query();

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        $groups = $query->paginate($perPage, ['*'], 'page', $page);

        return $this->paginatedResponse($groups, 'Groups retrieved successfully');
    }

    public function show(int $group): JsonResponse
    {
        $groupData = Group::with(['members.user'])->find($group);

        if (!$groupData) {
            return $this->errorResponse('Group not found', null, 404);
        }

        return $this->successResponse($groupData, 'Group retrieved successfully');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:groups,name',
            'description' => 'nullable|string',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'integer|exists:users,id',
        ]);

        $group = Group::create($validated);

        if (!empty($validated['member_ids'])) {
            $group->members()->sync($validated['member_ids']);
        }

        return $this->successResponse($group->load('members.user'), 'Group created successfully', 201);
    }

    public function update(Request $request, int $group): JsonResponse
    {
        $groupData = Group::find($group);

        if (!$groupData) {
            return $this->errorResponse('Group not found', null, 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:groups,name,' . $groupData->id,
            'description' => 'nullable|string',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'integer|exists:users,id',
        ]);

        $groupData->update($validated);

        if (array_key_exists('member_ids', $validated)) {
            $groupData->members()->sync($validated['member_ids']);
        }

        return $this->successResponse($groupData->load('members.user'), 'Group updated successfully');
    }

    public function destroy(int $group): JsonResponse
    {
        $groupData = Group::find($group);

        if (!$groupData) {
            return $this->errorResponse('Group not found', null, 404);
        }

        $groupData->delete();

        return $this->successResponse(null, 'Group deleted successfully');
    }
}
