<?php

namespace App\Services\Coupon;

use App\DTOs\CouponDTO;
use App\Repositories\Coupon\CouponRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Exception;

class CouponService
{
    public function __construct(
        private CouponRepositoryInterface $repository
    ) {}

    public function all(
        array $filters = []
    ) {
        return $this->repository->all(
            $filters
        );
    }

    public function active()
    {
        return $this->repository->active();
    }

    public function find(
        int $id
    ) {
        return $this->repository->find(
            $id
        );
    }

    public function create(
        CouponDTO $dto
    ) {

        return DB::transaction(
            fn() =>
                $this->repository->create(
                    $dto->toArray()
                )
        );
    }

    public function update(
        int $id,
        CouponDTO $dto
    ) {

        return DB::transaction(
            fn() =>
                $this->repository->update(
                    $id,
                    $dto->toArray()
                )
        );
    }

    public function delete(
        int $id
    ) {
        return $this->repository->delete(
            $id
        );
    }

    public function usageHistory(
        int $couponId
    ) {
        return $this->repository
            ->usageHistory(
                $couponId
            );
    }

    public function validateCoupon(
        string $code,
        int $userId,
        float $amount
    ) {

        $coupon =
            $this->repository
                ->findByCode(
                    $code
                );

        if (!$coupon) {
            throw new Exception(
                'Invalid coupon code'
            );
        }

        if (!$coupon->is_active) {
            throw new Exception(
                'Coupon inactive'
            );
        }

        if (
            now()->lt($coupon->start_date)
        ) {
            throw new Exception(
                'Coupon not started'
            );
        }

        if (
            now()->gt($coupon->end_date)
        ) {
            throw new Exception(
                'Coupon expired'
            );
        }

        if (
            $amount <
            $coupon->minimum_amount
        ) {
            throw new Exception(
                'Minimum amount not reached'
            );
        }

        if (
            $coupon->usage_limit
        ) {

            $totalUsage =
                $coupon
                    ->usages()
                    ->count();

            if (
                $totalUsage >=
                $coupon->usage_limit
            ) {
                throw new Exception(
                    'Coupon usage limit exceeded'
                );
            }
        }

        $userUsage =
            $coupon
                ->usages()
                ->where(
                    'user_id',
                    $userId
                )
                ->count();

        if (
            $userUsage >=
            $coupon->usage_per_user
        ) {
            throw new Exception(
                'Coupon already used maximum times'
            );
        }

        $discount = 0;

        if (
            $coupon->discount_type ===
            'fixed'
        ) {

            $discount =
                $coupon->discount_value;
        } else {

            $discount =
                ($amount *
                $coupon->discount_value)
                / 100;

            if (
                $coupon->max_discount_amount
            ) {

                $discount =
                    min(
                        $discount,
                        $coupon->max_discount_amount
                    );
            }
        }

        return [

            'coupon_id' =>
                $coupon->id,

            'coupon_code' =>
                $coupon->code,

            'discount' =>
                round($discount, 2),

            'final_amount' =>
                round(
                    $amount - $discount,
                    2
                )
        ];
    }
}
