<?php

namespace App\Repositories\Staff;

use App\Repositories\BaseRepositoryInterface;

interface StaffRepositoryInterface extends BaseRepositoryInterface
{
    public function findByUserId(int $userId): ?object;

    public function getByDepartment(string $department);

    public function getActiveStaff();
}
