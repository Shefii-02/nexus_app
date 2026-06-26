<?php

namespace App\Repositories\Course;

use App\Repositories\BaseRepositoryInterface;

interface CourseClassRepositoryInterface extends BaseRepositoryInterface
{
    public function getByCourse(int $courseId, array $filters = []);

    public function getToday(int $userId, string $accType);
}
