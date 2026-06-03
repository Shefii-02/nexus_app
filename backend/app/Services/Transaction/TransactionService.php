<?php

namespace App\Services\Transaction;

use App\Repositories\Transaction\TransactionRepositoryInterface;

class TransactionService
{
    public function __construct(
        private TransactionRepositoryInterface $repository
    ) {}

    public function all(
        array $filters = []
    ) {
        return $this->repository
            ->all($filters);
    }

    public function find(
        int $id
    ) {
        return $this->repository
            ->find($id);
    }

    public function summary()
    {
        $data =
            $this->repository
                ->summary();

        return [

            ...$data,

            'profit' =>
                $data['income']
                -
                $data['expense']
                -
                $data['refund']
        ];
    }
}
