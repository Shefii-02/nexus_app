<?php

namespace App\Repositories\Coupon;

use App\Models\Coupon;
use App\Models\CouponUsage;

class CouponRepository
implements CouponRepositoryInterface
{
    public function all(
        array $filters = []
    ) {

        $query = Coupon::query()
            ->withCount('usages');

        if (
            isset($filters['status'])
        ) {

            $query->where(
                'is_active',
                $filters['status']
            );
        }

        return $query
            ->latest()
            ->paginate(
                request('per_page', 20)
            );
    }

    public function active()
    {
        return Coupon::query()

            ->where(
                'is_active',
                1
            )

            ->whereDate(
                'start_date',
                '<=',
                now()
            )

            ->whereDate(
                'end_date',
                '>=',
                now()
            )

            ->get();
    }

    public function find(
        int $id
    ) {
        return Coupon::findOrFail(
            $id
        );
    }

    public function findByCode(
        string $code
    ) {
        return Coupon::where(
            'code',
            $code
        )->first();
    }

    public function create(
        array $data
    ) {
        return Coupon::create(
            $data
        );
    }

    public function update(
        int $id,
        array $data
    ) {

        $coupon =
            Coupon::findOrFail(
                $id
            );

        $coupon->update(
            $data
        );

        return $coupon->fresh();
    }

    public function delete(
        int $id
    ) {
        return Coupon::findOrFail(
            $id
        )->delete();
    }

    public function usageHistory(
        int $couponId
    ) {

        return CouponUsage::query()

            ->with([
                'coupon',
                'user'
            ])

            ->where(
                'coupon_id',
                $couponId
            )

            ->latest()

            ->paginate(
                request('per_page', 20)
            );
    }
}
