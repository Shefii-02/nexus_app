<?php

namespace App\Http\Controllers\API\Admin;

use App\DTO\TeacherPaymentDTO;
use App\Http\Requests\TeacherPaymentRequest;
use App\Http\Resources\TeacherPaymentResource;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\TeacherPayment\TeacherPaymentService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TeacherPaymentController extends Controller
{
    public function __construct(
        private readonly TeacherPaymentService $service
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $payments = $this->service->list($request->only([
            'search', 'status', 'teacher_id', 'per_page', 'page',
        ]));

        return TeacherPaymentResource::collection($payments);
    }

    public function store(TeacherPaymentRequest $request): TeacherPaymentResource
    {
        $payment = $this->service->create(
            TeacherPaymentDTO::fromRequest($request->validated())
        );

        return new TeacherPaymentResource($payment);
    }

    public function show(int $id): TeacherPaymentResource
    {
        return new TeacherPaymentResource(
            $this->service->find($id)
        );
    }

    public function update(TeacherPaymentRequest $request, int $id): TeacherPaymentResource
    {
        $payment = $this->service->update(
            $id,
            TeacherPaymentDTO::fromRequest($request->validated())
        );

        return new TeacherPaymentResource($payment);
    }

    public function release(int $id): TeacherPaymentResource
    {
        return new TeacherPaymentResource(
            $this->service->release($id)
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);
        return response()->json(['message' => 'Payment deleted successfully.']);
    }
}
