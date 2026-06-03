<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeacherPaymentItemRequest;
use App\Http\Resources\TeacherPaymentItemResource;
use App\Services\TeacherPaymentItem\TeacherPaymentItemService;
use App\Services\TeacherPayment\TeacherPaymentService;
use App\DTOs\TeacherPaymentItemDTO;
use App\Http\Controllers\API\ApiResponse;
use Illuminate\Http\Request;

class TeacherPaymentController
extends Controller
{
    use ApiResponse;

    public function __construct(
        private TeacherPaymentItemService $itemService,
        private TeacherPaymentService $paymentService
    ) {}

    public function pending()
    {
        return $this->paginatedResponse(
            TeacherPaymentItemResource::collection(
                $this->itemService->pending()
            )
        );
    }

    public function storeCalculation(
        TeacherPaymentItemRequest $request
    ) {

        return $this->successResponse(

            new TeacherPaymentItemResource(

                $this->itemService->create(

                    TeacherPaymentItemDTO::fromArray(
                        $request->validated()
                    )
                )
            )
        );
    }

    public function release(
        Request $request
    ) {

        return $this->successResponse(

            $this->paymentService->release(

                $request->item_ids,

                $request->payment_method,

                $request->transaction_no,

                $request->remarks
            ),

            'Teacher payment released'
        );
    }


    public function pendingByTeacher(
        int $teacherId
    ) {
        return \App\Models\TeacherPaymentItem::query()

            ->with([
                'teacher',
                'course'
            ])

            ->where(
                'teacher_id',
                $teacherId
            )

            ->where(
                'status',
                'pending'
            )

            ->get();
    }
}
