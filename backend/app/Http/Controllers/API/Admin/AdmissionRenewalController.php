<?php

namespace App\Http\Controllers\Api\Admin;

use App\DTOs\AdmissionRenewalDTO;
use App\Http\Controllers\API\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdmissionRenewalRequest;
use App\Http\Resources\AdmissionRenewalResource;
use App\Services\AdmissionRenewal\AdmissionRenewalService;

class AdmissionRenewalController extends Controller
{
    use ApiResponse;

    public function __construct(
        private AdmissionRenewalService $service
    ) {}

    public function due()
    {
        return $this->successResponse(
            AdmissionRenewalResource::collection(
                $this->service->due()
            )
        );
    }

    public function index()
    {
        return $this->paginatedResponse(
            AdmissionRenewalResource::collection(
                $this->service->all()
            )
        );
    }

    public function show($id)
    {
        return $this->successResponse(
            new AdmissionRenewalResource(
                $this->service->find($id)
            )
        );
    }

    public function store(
        AdmissionRenewalRequest $request
    ) {
        return $this->successResponse(
            new AdmissionRenewalResource(
                $this->service->create(
                    AdmissionRenewalDTO::fromArray(
                        $request->validated()
                    )
                )
            )
        );
    }

    public function markPaid($id)
    {
        return $this->successResponse(
            $this->service->markAsPaid($id),
            'Renewal paid successfully'
        );
    }
}
