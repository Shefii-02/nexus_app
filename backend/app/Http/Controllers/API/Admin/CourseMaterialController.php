<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\API\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\CourseMaterialRequest;
use App\Http\Resources\CourseMaterialResource;
use App\Models\CourseMaterial;
use App\Services\Media\MediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CourseMaterialController extends Controller
{
    use ApiResponse;

    public function __construct(
        private MediaService $mediaService
    ) {}


    public function index(int $courseId): JsonResponse
    {
        $page = request()->query('page', 1);
        $perPage = request()->query('per_page', 15);
        $filters = request()->query('filters', []);

        $query = CourseMaterial::query();

        if (!empty($filters['course_id'])) {
            $query->where('course_id', $courseId);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $materials = $query->with('course')->orderBy('order', 'asc')->paginate($perPage, ['*'], 'page', $page);

        return $this->paginatedResponse(CourseMaterialResource::collection($materials), 'Course materials retrieved successfully');
    }

    public function show(int $courseId,int $courseMaterial): JsonResponse
    {
        $material = CourseMaterial::with('course')->find($courseMaterial);

        if (!$material) {
            return $this->errorResponse('Course material not found', null, 404);
        }

        return $this->successResponse(CourseMaterialResource::make($material), 'Course material retrieved successfully');
    }

    public function store(CourseMaterialRequest $request, int $courseId): JsonResponse
    {
        $data = $request->validated();
        // 🔥 inject course_id from route
        $data['course_id'] = $courseId;

        if ($request->hasFile('file_url')) {
            $media = $this->mediaService->upload($request->file('file_url'), auth()->id(), 'courses/materials');
            $data['file_url'] = $media->id;
        }

        $material = CourseMaterial::create(array_merge($data, [
            'status' => $validated['status'] ?? 'active',
        ]));

        return $this->successResponse(CourseMaterialResource::make($material->load('course')), 'Course material created successfully', 201);
    }

    public function update(CourseMaterialRequest $request, int $courseId, int $courseMaterial): JsonResponse
    {
        $material = CourseMaterial::find($courseMaterial);

        if (!$material) {
            return $this->errorResponse('Course material not found', null, 404);
        }
        $current = $material;

        $data = $request->validated();
        if ($request->hasFile('file_url')) {

            // 1. delete old media
            if ($current->file_url && is_int($current->file_url)) {
                $this->mediaService->delete($current->file_url);
            }
            $media = $this->mediaService->upload($request->file('file_url'), auth()->id(), 'courses/materials');


            $data['file_url'] = $media->id;
        }

        $material->update($data);

        return $this->successResponse(CourseMaterialResource::make($material->load('course')), 'Course material updated successfully');
    }

    public function destroy(int $courseId, int $courseMaterial): JsonResponse
    {
        $material = CourseMaterial::find($courseMaterial);

        if (!$material) {
            return $this->errorResponse('Course material not found', null, 404);
        }

        if ($material->file_url && is_int($material->file_url)) {
            Log::info('File Deleting');
            $this->mediaService->delete($material->file_url);
        }

        $material->delete();

        return $this->successResponse(null, 'Course material deleted successfully');
    }
}
