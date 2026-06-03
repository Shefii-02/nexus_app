<?php

namespace App\Repositories\CourseClass;

use App\Repositories\BaseRepositoryInterface;

interface CourseClassRepositoryInterface extends BaseRepositoryInterface
{
    public function getByCourse(int $courseId);

    public function getByTeacher(int $teacherId);

    public function getUpcoming();

    public function getByDateRange($startDate, $endDate);
}
