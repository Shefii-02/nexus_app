<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\API\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\CourseRenewal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseRenewalController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $page = request()->query('page', 1);
        $perPage = request()->query('per_page', 15);
        $filters = request()->query('filters', []);

        $query = CourseRenewal::query();

        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        if (!empty($filters['course_id'])) {
            $query->where('course_id', $filters['course_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['month']) && !empty($filters['year'])) {
            $query->forMonth($filters['month'], $filters['year']);
        }

        $renewals = $query->with(['student.user', 'course'])->orderBy('renewal_date', 'desc')->paginate($perPage, ['*'], 'page', $page);

        return $this->paginatedResponse($renewals, 'Course renewals retrieved successfully');
    }

    public function show(int $courseRenewal): JsonResponse
    {
        $renewal = CourseRenewal::with(['student.user', 'course'])->find($courseRenewal);

        if (!$renewal) {
            return $this->errorResponse('Course renewal not found', null, 404);
        }

        return $this->successResponse($renewal, 'Course renewal retrieved successfully');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|integer|exists:users,id',
            'course_id' => 'required|integer|exists:courses,id',
            'renewal_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'status' => 'nullable|in:pending,verified,rejected,paid',
            'payment_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $renewal = CourseRenewal::create(array_merge($validated, [
            'status' => $validated['status'] ?? 'pending',
        ]));

        return $this->successResponse($renewal->load(['student.user', 'course']), 'Course renewal created successfully', 201);
    }

    public function update(Request $request, int $courseRenewal): JsonResponse
    {
        $renewal = CourseRenewal::find($courseRenewal);

        if (!$renewal) {
            return $this->errorResponse('Course renewal not found', null, 404);
        }

        $validated = $request->validate([
            'student_id' => 'nullable|integer|exists:users,id',
            'course_id' => 'nullable|integer|exists:courses,id',
            'renewal_date' => 'nullable|date',
            'amount' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:pending,verified,rejected,paid',
            'payment_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $renewal->update($validated);

        return $this->successResponse($renewal->load(['student.user', 'course']), 'Course renewal updated successfully');
    }

    public function destroy(int $courseRenewal): JsonResponse
    {
        $renewal = CourseRenewal::find($courseRenewal);

        if (!$renewal) {
            return $this->errorResponse('Course renewal not found', null, 404);
        }

        $renewal->delete();

        return $this->successResponse(null, 'Course renewal deleted successfully');
    }

    public function verify(int $courseRenewal): JsonResponse
    {
        $renewal = CourseRenewal::find($courseRenewal);

        if (!$renewal) {
            return $this->errorResponse('Course renewal not found', null, 404);
        }

        $renewal->verify();

        return $this->successResponse($renewal->load(['student.user', 'course']), 'Course renewal verified successfully');
    }

    public function reject(int $courseRenewal): JsonResponse
    {
        $renewal = CourseRenewal::find($courseRenewal);

        if (!$renewal) {
            return $this->errorResponse('Course renewal not found', null, 404);
        }

        $renewal->reject();

        return $this->successResponse($renewal->load(['student.user', 'course']), 'Course renewal rejected successfully');
    }
}
