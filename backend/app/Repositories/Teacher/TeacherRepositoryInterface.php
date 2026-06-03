<?php

namespace App\Repositories\Teacher;

use App\Repositories\BaseRepositoryInterface;

interface TeacherRepositoryInterface extends BaseRepositoryInterface
{
    public function findByUserId(int $userId): ?object;

    public function getActiveTeachers();

    public function findWithCourses(int $id);
}
