<?php

namespace App\Repositories\Student;

use App\Repositories\BaseRepositoryInterface;

interface StudentRepositoryInterface extends BaseRepositoryInterface
{
    public function findByUserId(int $userId): ?object;

    public function findByRollNumber(string $rollNumber): ?object;

    public function getByBatch(int $batchId);

    public function getActiveStudents();
}
