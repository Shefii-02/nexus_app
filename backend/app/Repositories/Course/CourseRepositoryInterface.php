<?php

namespace App\Repositories\Course;

use App\Repositories\BaseRepositoryInterface;

interface CourseRepositoryInterface extends BaseRepositoryInterface
{
    public function findByCode(string $code): ?object;

    public function getByTeacher(int $teacherId);

    public function getByBatch(int $batchId);

    public function getActiveCourses();
}
