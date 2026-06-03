<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\API\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Services\Transaction\TransactionService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    use ApiResponse;

    public function __construct(
        private TransactionService $service
    ) {}

    public function index(
        Request $request
    ) {

        return $this->paginatedResponse(

            TransactionResource::collection(

                $this->service->all(
                    $request->all()
                )
            ),

            'Transactions retrieved'
        );
    }

    public function show(
        int $id
    ) {

        return $this->successResponse(

            new TransactionResource(

                $this->service->find(
                    $id
                )
            )
        );
    }

    public function summary()
    {
        return $this->successResponse(
            $this->service->summary()
        );
    }
}
