<?php

namespace App\Http\Controllers\API\Admin;

use App\DTOs\TeacherPaymentDTO;
use App\Http\Requests\TeacherPaymentRequest;
use App\Http\Resources\TeacherPaymentResource;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Notification\FcmNotificationService;
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
            'search',
            'status',
            'teacher_id',
            'per_page',
            'page',
        ]));

        return TeacherPaymentResource::collection($payments);
    }

    public function store(TeacherPaymentRequest $request): TeacherPaymentResource
    {

        $dto = TeacherPaymentDTO::fromArray($request->validated());

        $payment = $this->service->create($dto);


        $payment->loadMissing('teacher'); // adjust relation name
        $teacherUserId = $payment->teacher?->user_id ?? $payment->teacher_id ?? null;
        if ($teacherUserId) {
            (new FcmNotificationService())->sendCustom(
                [$teacherUserId],
                '💰 Payment Entry Created',
                'A payment entry of ' . ($payment->amount ?? '') . ' has been recorded.',
                [
                    'type'       => 'payment_entry',
                    'payment_id' => (string) $payment->id,
                ]
            );
        }

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

        $this->service->release($id);

        $payment = $this->service->find($id); // reload after release
        $payment->loadMissing('teacher');
        $teacherUserId = $payment->teacher?->user_id ?? $payment->teacher_id ?? null;
        if ($teacherUserId) {
            (new FcmNotificationService())->sendCustom(
                [$teacherUserId],
                '✅ Payment Released',
                'Your payment of ' . ($payment->amount ?? '') . ' has been released.',
                [
                    'type'       => 'payment_released',
                    'payment_id' => (string) $payment->id,
                ]
            );
        }

        return new TeacherPaymentResource(
            $payment
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);
        return response()->json(['message' => 'Payment deleted successfully.']);
    }
}
