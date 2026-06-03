<?php

namespace App\Repositories\AdmissionPayment;

interface AdmissionPaymentRepositoryInterface
{
    public function all(array $filters = []);

    public function find(int $id);

    public function create(array $data);
}
