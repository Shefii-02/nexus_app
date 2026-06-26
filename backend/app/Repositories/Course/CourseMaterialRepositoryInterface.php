<?php

namespace App\Repositories\Course;

use App\Repositories\BaseRepositoryInterface;

interface CourseMaterialRepositoryInterface extends BaseRepositoryInterface
{
    public function getByCourse(int $courseId, array $filters = []);

    public function reorder(int $courseId, array $orderedIds): bool;
}
