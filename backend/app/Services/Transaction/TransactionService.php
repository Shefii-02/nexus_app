<?php

namespace App\Services;

use App\DTOs\TransactionDTO;
use App\Models\Transaction;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class TransactionService
{
    public function list(array $filters): LengthAwarePaginator
    {
        $q = Transaction::query()->orderByDesc('transaction_date')->orderByDesc('id');

        if (!empty($filters['type'])) {
            $q->where('type', $filters['type']);
        }
        if (!empty($filters['category'])) {
            $q->where('category', $filters['category']);
        }
        if (!empty($filters['search'])) {
            $q->where(function ($sub) use ($filters) {
                $sub->where('transaction_no', 'like', "%{$filters['search']}%")
                    ->orWhere('description',   'like', "%{$filters['search']}%");
            });
        }
        if (!empty($filters['from'])) {
            $q->whereDate('transaction_date', '>=', $filters['from']);
        }
        if (!empty($filters['to'])) {
            $q->whereDate('transaction_date', '<=', $filters['to']);
        }

        return $q->paginate((int) ($filters['per_page'] ?? 15));
    }

    public function create(TransactionDTO $dto): Transaction
    {
        $data = $dto->toArray();
        $data['transaction_no'] = $this->generateTransactionNo($dto->type);
        return Transaction::create($data);
    }

    public function find(int $id): Transaction
    {
        return Transaction::findOrFail($id);
    }

    public function update(Transaction $transaction, TransactionDTO $dto): Transaction
    {
        $transaction->update($dto->toArray());
        return $transaction->fresh();
    }

    public function delete(Transaction $transaction): void
    {
        $transaction->delete();
    }

    private function generateTransactionNo(string $type): string
    {
        $prefix = match ($type) {
            'income'  => 'INC',
            'expense' => 'EXP',
            'refund'  => 'REF',
            default   => 'TXN',
        };
        return $prefix . '-' . strtoupper(Str::random(8)) . '-' . now()->format('Ymd');
    }
}
