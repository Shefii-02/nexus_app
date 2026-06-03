<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\API\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\CourseClassLink;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseClassLinkController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $page = request()->query('page', 1);
        $perPage = request()->query('per_page', 15);
        $filters = request()->query('filters', []);

        $query = CourseClassLink::query();

        if (!empty($filters['course_class_id'])) {
            $query->where('course_class_id', $filters['course_class_id']);
        }

        if (!empty($filters['source'])) {
            $query->where('source', $filters['source']);
        }

        $links = $query->with('courseClass')->paginate($perPage, ['*'], 'page', $page);

        return $this->paginatedResponse($links, 'Course class links retrieved successfully');
    }

    public function show(int $courseClassLink): JsonResponse
    {
        $link = CourseClassLink::with('courseClass')->find($courseClassLink);

        if (!$link) {
            return $this->errorResponse('Course class link not found', null, 404);
        }

        return $this->successResponse($link, 'Course class link retrieved successfully');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'course_class_id' => 'required|integer|exists:course_classes,id',
            'class_link' => 'nullable|string|max:1000',
            'record_link' => 'nullable|string|max:1000',
            'source' => 'nullable|string|in:google_meet,zoom,teams,other',
        ]);

        $link = CourseClassLink::updateOrCreate(
            ['course_class_id' => $validated['course_class_id']],
            $validated
        );

        return $this->successResponse($link->load('courseClass'), 'Course class link stored successfully', 201);
    }

    public function update(Request $request, int $courseClassLink): JsonResponse
    {
        $link = CourseClassLink::find($courseClassLink);

        if (!$link) {
            return $this->errorResponse('Course class link not found', null, 404);
        }

        $validated = $request->validate([
            'course_class_id' => 'nullable|integer|exists:course_classes,id',
            'class_link' => 'nullable|string|max:1000',
            'record_link' => 'nullable|string|max:1000',
            'source' => 'nullable|string|in:google_meet,zoom,teams,other',
        ]);

        $link->update($validated);

        return $this->successResponse($link->load('courseClass'), 'Course class link updated successfully');
    }

    public function destroy(int $courseClassLink): JsonResponse
    {
        $link = CourseClassLink::find($courseClassLink);

        if (!$link) {
            return $this->errorResponse('Course class link not found', null, 404);
        }

        $link->delete();

        return $this->successResponse(null, 'Course class link deleted successfully');
    }
}
