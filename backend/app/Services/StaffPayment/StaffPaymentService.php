<?php

namespace App\Services\StaffPayment;

use App\DTOs\StaffPaymentDTO;
use App\Models\Transaction;
use App\Repositories\StaffPayment\StaffPaymentRepositoryInterface;
use Illuminate\Support\Facades\DB;

class StaffPaymentService
{
    public function __construct(
        private StaffPaymentRepositoryInterface $repository
    ) {}

    public function all(
        array $filters = []
    ) {
        return $this->repository->all(
            $filters
        );
    }

    public function pending()
    {
        return $this->repository
            ->pending();
    }

    public function history()
    {
        return $this->repository
            ->history();
    }

    public function find(
        int $id
    ) {
        return $this->repository
            ->find($id);
    }

    public function create(
        StaffPaymentDTO $dto
    ) {

        return DB::transaction(
            function () use ($dto) {

                return $this->repository
                    ->create(
                        $dto->toArray()
                    );
            }
        );
    }

    public function update(
        int $id,
        StaffPaymentDTO $dto
    ) {

        return DB::transaction(
            function () use (
                $id,
                $dto
            ) {

                return $this->repository
                    ->update(
                        $id,
                        $dto->toArray()
                    );
            }
        );
    }

    public function release(
        int $id,
        string $paymentMethod,
        ?string $transactionNo = null
    ) {

        return DB::transaction(
            function () use (
                $id,
                $paymentMethod,
                $transactionNo
            ) {

                $payment =
                    $this->repository
                        ->find($id);

                $payment->update([

                    'status' =>
                        'released',

                    'payment_method' =>
                        $paymentMethod,

                    'transaction_no' =>
                        $transactionNo,

                    'payment_date' =>
                        now(),

                    'released_by' =>
                        auth()->id(),
                ]);

                Transaction::create([

                    'type' =>
                        'expense',

                    'category' =>
                        'staff_payment',

                    'reference_type' =>
                        'staff_payment',

                    'reference_id' =>
                        $payment->id,

                    'amount' =>
                        $payment->final_amount,

                    'payment_method' =>
                        $paymentMethod,

                    'transaction_no' =>
                        $transactionNo,

                    'transaction_date' =>
                        now(),

                    'description' =>
                        'Staff salary released',

                    'created_by' =>
                        auth()->id(),
                ]);

                return $payment->fresh();
            }
        );
    }

    public function delete(
        int $id
    )
    {
        $payment =
            $this->repository
                ->find($id);

        if (
            $payment->status ===
            'released'
        ) {
            throw new \Exception(
                'Released payment cannot be deleted'
            );
        }

        return $payment->delete();
    }
}
