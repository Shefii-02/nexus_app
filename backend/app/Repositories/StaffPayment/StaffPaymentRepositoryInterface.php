<?php

namespace App\Repositories\StaffPayment;

interface StaffPaymentRepositoryInterface
{
    public function all(
        array $filters = []
    );

    public function pending();

    public function history();

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

    public function delete(
        int $id
    );
}
