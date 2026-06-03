<?php

namespace App\Http\Controllers\Api\Admin;

use App\DTOs\LeaveRequestDTO;
use App\Http\Controllers\API\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\LeaveRequestRequest;
use App\Http\Resources\LeaveRequestResource;
use App\Services\LeaveRequest\LeaveRequestService;

class LeaveRequestController extends Controller
{
    use ApiResponse;
    public function __construct(
        private LeaveRequestService $service
    ) {}

    public function index()
    {
        return $this->paginatedResponse(
            LeaveRequestResource::collection(
                $this->service->all()
            )
        );
    }

    public function store(
        LeaveRequestRequest $request
    )
    {
        return $this->successResponse(

            new LeaveRequestResource(

                $this->service->create(

                    LeaveRequestDTO::fromArray(
                        $request->validated()
                    )
                )
            ),

            'Leave request submitted'
        );
    }

    public function show(int $id)
    {
        return $this->successResponse(

            new LeaveRequestResource(

                $this->service->find($id)
            )
        );
    }

    public function approve(
        int $id
    )
    {
        return $this->successResponse(

            new LeaveRequestResource(

                $this->service->approve($id)
            ),

            'Leave approved'
        );
    }

    public function reject(
        int $id
    )
    {
        return $this->successResponse(

            new LeaveRequestResource(

                $this->service->reject($id)
            ),

            'Leave rejected'
        );
    }

    public function destroy(
        int $id
    )
    {
        $this->service->delete(
            $id
        );

        return $this->successResponse(
            null,
            'Deleted successfully'
        );
    }
}
