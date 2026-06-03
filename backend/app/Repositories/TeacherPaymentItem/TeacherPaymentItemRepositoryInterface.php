<?php

namespace App\Repositories\TeacherPaymentItem;

interface TeacherPaymentItemRepositoryInterface
{
    public function all(
        array $filters = []
    );

    public function pending();

    public function find(
        int $id
    );

    public function create(
        array $data
    );

    public function update(
        int $id,
        array $data
    );

    public function pendingByTeacher(
        int $teacherId
    );
}
