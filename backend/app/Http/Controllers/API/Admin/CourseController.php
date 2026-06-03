<?php

namespace App\Http\Controllers\API\Admin;

use App\DTOs\CourseDTO;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\ApiResponse;
use App\Http\Requests\CourseRequest;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Http\Resources\CourseResource;
use App\Services\Course\CourseService;
use Illuminate\Http\JsonResponse;
use App\Services\Media\MediaService;
use Illuminate\Support\Facades\Log;

class CourseController extends Controller
{
    use ApiResponse;

    public function __construct(
        private CourseService $courseService,
        private MediaService $mediaService
    ) {}


    public function index(): JsonResponse
    {
        $page = request()->query('page', 1);
        $perPage = request()->query('per_page', 15);
        $filters = request()->query('filters', []);

        $courses = $this->courseService->list($page, $perPage, $filters);

        return $this->paginatedResponse(CourseResource::collection($courses), 'Courses retrieved successfully');
    }

    public function show(int $course): JsonResponse
    {
        $courseData = $this->courseService->findWithRelations($course, ['teacher.user']);

        if (!$courseData) {
            return $this->errorResponse('Course not found', null, 404);
        }

        return $this->successResponse(CourseResource::make($courseData), 'Course retrieved successfully');
    }

    public function store(CourseRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            // 🔥 handle file upload


            if ($request->hasFile('thumbnail')) {
                $media = $this->mediaService->upload($request->file('thumbnail'), auth()->id(), 'courses');
                $data['thumbnail'] = $media->id;
            }

            $dto = CourseDTO::fromArray($data);
            $course = $this->courseService->create($dto);

            return $this->successResponse(
                CourseResource::make($course->load(['teacher.user', 'batch'])),
                'Course created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create course', ['error' => $e->getMessage()], 500);
        }
    }

    public function update(CourseRequest $request, int $course): JsonResponse
    {
        try {

            if (!$this->courseService->exists($course)) {
                return $this->errorResponse('Course not found', null, 404);
            }

            $current = $this->courseService->find($course);

            $data = $request->validated();


            // if ($request->input('thumbnail') === null && $current->thumbnail) {
            //     $this->mediaService->delete($current->thumbnail);
            //     $data['thumbnail'] = null;
            // }

            if ($request->hasFile('thumbnail')) {

                // 1. delete old media
                if ($current->thumbnail && is_int($current->thumbnail)) {
                    $this->mediaService->delete($current->thumbnail);
                }
                $media = $this->mediaService->upload($request->file('thumbnail'), auth()->id(), 'courses');


                $data['thumbnail'] = $media->id;
            }


            $dto = CourseDTO::fromArray(array_merge(
                $current->toArray(),
                $data
            ));



            $this->courseService->update($course, $dto);
            $updated = $this->courseService->findWithRelations($course, ['teacher.user', 'batch']);

            return $this->successResponse(CourseResource::make($updated), 'Course updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update course', ['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(int $course): JsonResponse
    {
        try {
            if (!$this->courseService->exists($course)) {
                return $this->errorResponse('Course not found', null, 404);
            }

            $current = $this->courseService->find($course);
            if ($current->thumbnail && is_int($current->thumbnail)) {
                $this->mediaService->delete($current->thumbnail);
            }

            $this->courseService->delete($course);

            return $this->successResponse(null, 'Course deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete course', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Attach student to course
     */
    public function attachStudent(int $course, int $student): JsonResponse
    {
        try {
            if (!$this->courseService->exists($course)) {
                return $this->errorResponse('Course not found', null, 404);
            }

            $this->courseService->attachStudent($course, $student);

            return $this->successResponse(null, 'Student attached to course successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to attach student', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Detach student from course
     */
    public function detachStudent(int $course, int $student): JsonResponse
    {
        try {
            if (!$this->courseService->exists($course)) {
                return $this->errorResponse('Course not found', null, 404);
            }

            $this->courseService->detachStudent($course, $student);

            return $this->successResponse(null, 'Student detached from course successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to detach student', ['error' => $e->getMessage()], 500);
        }
    }
}
