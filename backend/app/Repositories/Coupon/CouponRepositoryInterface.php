<?php

namespace App\Repositories\Coupon;

interface CouponRepositoryInterface
{
    public function all(
        array $filters = []
    );

    public function active();

    public function find(
        int $id
    );

    public function findByCode(
        string $code
    );

    public function create(
        array $data
    );

    public function update(
        int $id,
        array $data
    );

    public function delete(
        int $id
    );

    public function usageHistory(
        int $couponId
    );
}
