<?php

namespace App\Repositories\Transaction;

interface TransactionRepositoryInterface
{
    public function all(array $filters = []);

    public function find(int $id);

    public function summary();
}
