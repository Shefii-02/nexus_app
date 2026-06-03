<?php

namespace App\Http\Controllers\Api\Admin;

use App\DTOs\CouponDTO;
use App\Http\Controllers\API\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\CouponRequest;
use App\Http\Resources\CouponResource;
use App\Http\Resources\CouponUsageResource;
use App\Services\Coupon\CouponService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    use ApiResponse;
    public function __construct(
        private CouponService $service
    ) {}

    /**
     * List
     */
    public function index(
        Request $request
    ): JsonResponse {

        $coupons = $this->service->all(
            $request->all()
        );

        return $this->paginatedResponse(
            CouponResource::collection(
                $coupons
            ),
            'Coupons retrieved successfully'
        );
    }

    /**
     * Create
     */
    public function store(
        CouponRequest $request
    ): JsonResponse {

        $coupon = $this->service->create(

            CouponDTO::fromArray(
                $request->validated()
            )
        );

        return $this->successResponse(

            new CouponResource(
                $coupon
            ),

            'Coupon created successfully'
        );
    }

    /**
     * Show
     */
    public function show(
        int $id
    ): JsonResponse {

        $coupon = $this->service->find(
            $id
        );

        return $this->successResponse(

            new CouponResource(
                $coupon
            ),

            'Coupon retrieved successfully'
        );
    }

    /**
     * Update
     */
    public function update(
        CouponRequest $request,
        int $id
    ): JsonResponse {

        $coupon = $this->service->update(

            $id,

            CouponDTO::fromArray(
                $request->validated()
            )
        );

        return $this->successResponse(

            new CouponResource(
                $coupon
            ),

            'Coupon updated successfully'
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
            'Coupon deleted successfully'
        );
    }

    /**
     * Validate Coupon
     */
    public function validateCoupon(
        Request $request
    ): JsonResponse {

        $request->validate([

            'code' => [
                'required'
            ],

            'user_id' => [
                'required'
            ],

            'amount' => [
                'required',
                'numeric'
            ]
        ]);

        $coupon = $this->service
            ->validateCoupon(

                $request->code,

                $request->user_id,

                $request->amount
            );

        return $this->successResponse(

            $coupon,

            'Coupon valid'
        );
    }

    /**
     * Usage History
     */
    public function usageHistory(
        int $id
    ): JsonResponse {

        $history = $this->service
            ->usageHistory(
                $id
            );

        return $this->paginatedResponse(

            CouponUsageResource::collection(
                $history
            ),

            'Coupon usage history retrieved successfully'
        );
    }

    /**
     * Active Coupons
     */
    public function active(): JsonResponse
    {
        $coupons = $this->service
            ->active();

        return $this->successResponse(

            CouponResource::collection(
                $coupons
            ),

            'Active coupons retrieved successfully'
        );
    }
}
