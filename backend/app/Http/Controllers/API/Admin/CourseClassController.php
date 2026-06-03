<?php

namespace App\Http\Controllers\API\Admin;

use App\DTOs\CourseClassDTO;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\ApiResponse;
use App\Http\Requests\CourseClassRequest;
use App\Http\Requests\StoreCourseClassRequest;
use App\Http\Requests\UpdateCourseClassRequest;
use App\Http\Resources\CourseClassResource;
use App\Services\CourseClass\CourseClassService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CourseClassController extends Controller
{
    use ApiResponse;

    public function __construct(private CourseClassService $courseClassService) {}

    public function index($courseId): JsonResponse
    {
        $page = request()->query('page', 1);
        $perPage = request()->query('per_page', 15);
        $filters = request()->query('filters', []);
        // 🔥 FORCE course filter
        $filters['course_id'] = $courseId;

        $classes = $this->courseClassService->list($page, $perPage, $filters);

        return $this->paginatedResponse(CourseClassResource::collection($classes), 'Course classes retrieved successfully');
    }

    public function show($courseId, int $courseClass): JsonResponse
    {
        $classData = $this->courseClassService->findWithRelations($courseClass, ['course', 'teacher.user']);

        if (!$classData) {
            return $this->errorResponse('Course class not found', null, 404);
        }

        return $this->successResponse(CourseClassResource::make($classData), 'Course class retrieved successfully');
    }

    public function store(CourseClassRequest $request, int $courseId): JsonResponse
    {
        try {
            $data = $request->validated();

            // 🔥 inject course_id from route
            $data['course_id'] = $courseId;

            $dto = CourseClassDTO::fromArray($data);
            $class = $this->courseClassService->create($dto);

            return $this->successResponse(
                CourseClassResource::make($class->load(['course', 'teacher.user'])),
                'Course class created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create course class', ['error' => $e->getMessage()], 500);
        }
    }

    public function update(CourseClassRequest $request, int $courseId, int $courseClass,): JsonResponse
    {
        try {
            if (!$this->courseClassService->exists($courseClass)) {
                return $this->errorResponse('Course class not found', null, 404);
            }

            $current = $this->courseClassService->find($courseClass);
            $data = array_merge(
                $current->toArray(),
                $request->validated()
            );

            // 🔥 enforce correct course_id
            $data['course_id'] = $courseId;

            $dto = CourseClassDTO::fromArray($data);

            // $dto = CourseClassDTO::fromArray(array_merge(
            //     $current->toArray(),
            //     $request->validated()
            // ));

            $this->courseClassService->update($courseClass, $dto);
            $updated = $this->courseClassService->findWithRelations($courseClass, ['course', 'teacher.user']);

            return $this->successResponse(CourseClassResource::make($updated), 'Course class updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update course class', ['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(int $courseClass): JsonResponse
    {
        try {
            if (!$this->courseClassService->exists($courseClass)) {
                return $this->errorResponse('Course class not found', null, 404);
            }

            $this->courseClassService->delete($courseClass);

            return $this->successResponse(null, 'Course class deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete course class', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get upcoming classes
     */
    public function upcoming(): JsonResponse
    {
        try {
            $classes = $this->courseClassService->getUpcoming();
            return $this->paginatedResponse(CourseClassResource::collection($classes), 'Upcoming classes retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve upcoming classes', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get classes by date range
     */
    public function byDateRange(Request $request): JsonResponse
    {

        try {
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            if (!$startDate || !$endDate) {
                return $this->errorResponse('start_date and end_date are required');
            }

            $classes = $this->courseClassService->getByDateRange($startDate, $endDate);
            return $this->paginatedResponse(CourseClassResource::collection($classes), 'Classes retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve classes', ['error' => $e->getMessage()], 500);
        }
    }
}
