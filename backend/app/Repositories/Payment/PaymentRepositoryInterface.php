<?php

namespace App\Repositories\Payment;

use App\Repositories\BaseRepositoryInterface;

interface PaymentRepositoryInterface extends BaseRepositoryInterface
{
    public function getByStudent(int $studentId);

    public function getByCourse(int $courseId);

    public function getByStudentAndCourse(int $studentId, int $courseId);

    public function getPending();

    public function getVerified();

    public function getTotalByStudent(int $studentId);
}
