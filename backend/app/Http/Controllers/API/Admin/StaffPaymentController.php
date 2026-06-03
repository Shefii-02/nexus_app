<?php

namespace App\Http\Controllers\Api\Admin;

use App\DTOs\StaffPaymentDTO;
use App\Http\Controllers\API\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StaffPaymentRequest;
use App\Http\Resources\StaffPaymentResource;
use App\Services\StaffPayment\StaffPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StaffPaymentController extends Controller
{

    use ApiResponse;
    public function __construct(
        private StaffPaymentService $service
    ) {}

    /**
     * All
     */
    public function index(
        Request $request
    ): JsonResponse {

        $payments =
            $this->service->all(
                $request->all()
            );

        return $this->paginatedResponse(

            StaffPaymentResource::collection(
                $payments
            ),

            'Staff payments retrieved successfully'
        );
    }

    /**
     * Pending
     */
    public function pending(): JsonResponse
    {
        $payments =
            $this->service->pending();

        return $this->paginatedResponse(

            StaffPaymentResource::collection(
                $payments
            ),

            'Pending staff payments retrieved successfully'
        );
    }

    /**
     * History
     */
    public function history(): JsonResponse
    {
        $payments =
            $this->service->history();

        return $this->paginatedResponse(

            StaffPaymentResource::collection(
                $payments
            ),

            'Released staff payments retrieved successfully'
        );
    }

    /**
     * Show
     */
    public function show(
        int $id
    ): JsonResponse {

        return $this->successResponse(

            new StaffPaymentResource(

                $this->service->find(
                    $id
                )
            ),

            'Staff payment retrieved successfully'
        );
    }

    /**
     * Create Salary Entry
     */
    public function store(
        StaffPaymentRequest $request
    ): JsonResponse {

        $payment =
            $this->service->create(

                StaffPaymentDTO::fromArray(
                    $request->validated()
                )
            );

        return $this->successResponse(

            new StaffPaymentResource(
                $payment
            ),

            'Staff payment created successfully'
        );
    }

    /**
     * Update
     */
    public function update(
        StaffPaymentRequest $request,
        int $id
    ): JsonResponse {

        $payment =
            $this->service->update(

                $id,

                StaffPaymentDTO::fromArray(
                    $request->validated()
                )
            );

        return $this->successResponse(

            new StaffPaymentResource(
                $payment
            ),

            'Staff payment updated successfully'
        );
    }

    /**
     * Release Salary
     */
    public function release(
        Request $request,
        int $id
    ): JsonResponse {

        $request->validate([

            'payment_method' => [
                'required'
            ],

            'transaction_no' => [
                'nullable'
            ]
        ]);

        $payment =
            $this->service->release(

                $id,

                $request->payment_method,

                $request->transaction_no
            );

        return $this->successResponse(

            new StaffPaymentResource(
                $payment
            ),

            'Salary released successfully'
        );
    }

    /**
     * Delete
     */
    public function destroy(
        int $id
    ): JsonResponse {

        $this->service->delete(
            $id
        );

        return $this->successResponse(
            null,
            'Staff payment deleted successfully'
        );
    }
}
