<?php

namespace App\Http\Controllers\Api\Admin;

use App\DTOs\AdmissionPaymentDTO;
use App\Http\Controllers\API\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdmissionPaymentRequest;
use App\Http\Resources\AdmissionPaymentResource;
use App\Services\AdmissionPayment\AdmissionPaymentService;

class AdmissionPaymentController extends Controller
{
    use ApiResponse;


    public function __construct(
        private AdmissionPaymentService $service
    ) {}


    public function index()
    {
        return $this->paginatedResponse(
            AdmissionPaymentResource::collection(
                $this->service->all()
            )
        );
    }

    public function show($id)
    {
        return $this->successResponse(
            new AdmissionPaymentResource(
                $this->service->find($id)
            )
        );
    }

    public function store(
        AdmissionPaymentRequest $request
    ) {

        return $this->successResponse(
            new AdmissionPaymentResource(
                $this->service->create(
                    AdmissionPaymentDTO::fromArray(
                        $request->validated()
                    )
                )
            ),
            'Payment received'
        );
    }
}
