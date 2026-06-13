<?php

namespace App\Http\Controllers\API\Admin;

use App\DTOs\CourseClassDTO;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\ApiResponse;
use App\Http\Requests\CourseClassRequest;
use App\Http\Requests\StoreCourseClassRequest;
use App\Http\Requests\UpdateCourseClassRequest;
use App\Http\Resources\CourseClassResource;
use App\Models\CourseClass;
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
        // $filters = request()->query('filters', []);


        $filters = [
            'search' => request('search'),
            'record_link' => request('record_link'),
            'status' => request('status'),
            'source' => request('source'),
            'class_no' => request('class_no'),
            'teacher_id' => request('teacher_id'),
            'start_date' => request('start_date'),
            'end_date' => request('end_date'),
        ];


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

    public function today(Request $request): JsonResponse
    {
        $today = now()->toDateString();

        $classes = CourseClass::with('teacher:id,name,avatar')
            ->whereDate('start_time', $today)
            ->orderBy('start_time')
            ->get()
            ->map(function ($cls) {
                $now    = now();
                $status = $cls->status; // use DB status if you store it

                // Derive status from time if not explicitly set
                if ($status !== 'completed') {
                    if ($now->between($cls->start_time, $cls->end_time)) {
                        $status = 'live';
                    } elseif ($now->lt($cls->start_time)) {
                        $status = 'upcoming';
                    } else {
                        $status = 'completed';
                    }
                }

                return [
                    'id'          => $cls->id,
                    'title'       => $cls->title,
                    'description' => $cls->description,
                    'start_time'  => $cls->start_time->toISOString(),
                    'end_time'    => $cls->end_time->toISOString(),
                    'status'      => $status,
                    'class_link'  => $cls->class_link,
                    'record_link' => $cls->record_link,
                    'teacher'     => $cls->teacher ? [
                        'id'     => $cls->teacher->id,
                        'name'   => $cls->teacher->name,
                        'avatar' => $cls->teacher->avatar,
                    ] : null,
                ];
            });

        return response()->json(['data' => $classes]);
    }
}
