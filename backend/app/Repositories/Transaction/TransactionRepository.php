<?php

namespace App\Repositories\Transaction;

use App\Models\Transaction;

class TransactionRepository
implements TransactionRepositoryInterface
{
    public function all(
        array $filters = []
    ) {

        return Transaction::query()

            ->when(
                !empty($filters['type']),
                fn($q) =>
                $q->where(
                    'type',
                    $filters['type']
                )
            )

            ->when(
                !empty($filters['category']),
                fn($q) =>
                $q->where(
                    'category',
                    $filters['category']
                )
            )

            ->when(
                !empty($filters['from']),
                fn($q) =>
                $q->whereDate(
                    'transaction_date',
                    '>=',
                    $filters['from']
                )
            )

            ->when(
                !empty($filters['to']),
                fn($q) =>
                $q->whereDate(
                    'transaction_date',
                    '<=',
                    $filters['to']
                )
            )

            ->latest(
                'transaction_date'
            )

            ->paginate(
                $filters['per_page']
                    ?? 20
            );
    }

    public function find(
        int $id
    ) {
        return Transaction::findOrFail(
            $id
        );
    }

    public function summary()
    {
        return [

            'income' =>
                Transaction::where(
                    'type',
                    'income'
                )->sum('amount'),

            'expense' =>
                Transaction::where(
                    'type',
                    'expense'
                )->sum('amount'),

            'refund' =>
                Transaction::where(
                    'type',
                    'refund'
                )->sum('amount'),
        ];
    }
}
