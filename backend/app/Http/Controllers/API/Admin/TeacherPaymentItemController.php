<?php

namespace App\Http\Controllers\Api\Admin;

use App\DTOs\TeacherPaymentItemDTO;
use App\Http\Controllers\API\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\TeacherPaymentItemRequest;
use App\Http\Resources\TeacherPaymentItemResource;
use App\Services\TeacherPaymentItem\TeacherPaymentItemService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeacherPaymentItemController extends Controller
{
    use ApiResponse;


    public function __construct(
        private TeacherPaymentItemService $service
    ) {}

    /**
     * List all calculations
     */
    public function index(
        Request $request
    ): JsonResponse {

        $items = $this->service->all(
            $request->all()
        );

        return $this->paginatedResponse(
            TeacherPaymentItemResource::collection(
                $items
            ),
            'Teacher payment calculations retrieved successfully'
        );
    }

    /**
     * Pending calculations
     */
    public function pending(): JsonResponse
    {
        $items = $this->service->pending();

        return $this->paginatedResponse(
            TeacherPaymentItemResource::collection(
                $items
            ),
            'Pending teacher payment calculations retrieved successfully'
        );
    }

    /**
     * Pending by teacher
     */
    public function pendingByTeacher(
        int $teacherId
    ): JsonResponse {

        $items = $this->service
            ->pendingByTeacher(
                $teacherId
            );

        return $this->successResponse(
            TeacherPaymentItemResource::collection(
                $items
            ),
            'Teacher pending calculations retrieved successfully'
        );
    }

    /**
     * Show
     */
    public function show(
        int $id
    ): JsonResponse {

        $item = $this->service->find(
            $id
        );

        return $this->successResponse(
            new TeacherPaymentItemResource(
                $item
            ),
            'Teacher payment calculation retrieved successfully'
        );
    }

    /**
     * Create calculation
     */
    public function store(
        TeacherPaymentItemRequest $request
    ): JsonResponse {

        $item = $this->service->create(

            TeacherPaymentItemDTO::fromArray(
                $request->validated()
            )
        );

        return $this->successResponse(

            new TeacherPaymentItemResource(
                $item
            ),

            'Teacher payment calculation created successfully'
        );
    }

    /**
     * Update calculation
     */
    public function update(
        TeacherPaymentItemRequest $request,
        int $id
    ): JsonResponse {

        $item = $this->service->update(

            $id,

            TeacherPaymentItemDTO::fromArray(
                $request->validated()
            )
        );

        return $this->successResponse(

            new TeacherPaymentItemResource(
                $item
            ),

            'Teacher payment calculation updated successfully'
        );
    }

    /**
     * Delete calculation
     */
    public function destroy(
        int $id
    ): JsonResponse {

        $this->service->delete(
            $id
        );

        return $this->successResponse(
            null,
            'Teacher payment calculation deleted successfully'
        );
    }


    public function summary(
        int $teacherId
    ): JsonResponse {

        $pending =
            $this->service
            ->pendingByTeacher(
                $teacherId
            );

        return $this->successResponse([

            'total_items' =>
            $pending->count(),

            'total_amount' =>
            $pending->sum('amount'),

            'teacher_id' =>
            $teacherId
        ]);
    }
}
