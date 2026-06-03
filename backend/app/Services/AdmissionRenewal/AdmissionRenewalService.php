<?php

namespace App\Services\AdmissionRenewal;

use App\DTOs\AdmissionRenewalDTO;
use App\Models\Admission;
use App\Models\AdmissionPayment;
use App\Models\Transaction;
use App\Repositories\AdmissionRenewal\AdmissionRenewalRepositoryInterface;
use Illuminate\Support\Facades\DB;

class AdmissionRenewalService
{
    public function __construct(
        private AdmissionRenewalRepositoryInterface $repository
    ) {}

    public function all(array $filters = [])
    {
        return $this->repository->all($filters);
    }

    public function due()
    {
        return $this->repository->due();
    }

    public function find(int $id)
    {
        return $this->repository->find($id);
    }

    public function create(
        AdmissionRenewalDTO $dto
    ) {
        return $this->repository->create(
            [
                ...$dto->toArray(),
                'created_by' => auth()->id()
            ]
        );
    }

    public function markAsPaid(
        int $renewalId,
        string $paymentMethod = 'cash'
    ) {

        return DB::transaction(
            function () use (
                $renewalId,
                $paymentMethod
            ) {

                $renewal =
                    $this->repository->find(
                        $renewalId
                    );

                $renewal->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);

                $admission =
                    Admission::findOrFail(
                        $renewal->admission_id
                    );

                $admission->update([
                    'expiry_date' =>
                        $renewal->renewal_to
                ]);

                $payment =
                    AdmissionPayment::create([
                        'admission_id' =>
                            $admission->id,

                        'amount' =>
                            $renewal->final_amount,

                        'payment_method' =>
                            $paymentMethod,

                        'paid_at' =>
                            now(),

                        'created_by' =>
                            auth()->id()
                    ]);

                Transaction::create([
                    'type' => 'income',

                    'category' =>
                        'renewal_fee',

                    'reference_type' =>
                        'admission_renewal',

                    'reference_id' =>
                        $renewal->id,

                    'amount' =>
                        $renewal->final_amount,

                    'transaction_date' =>
                        now(),

                    'created_by' =>
                        auth()->id()
                ]);

                return $renewal;
            }
        );
    }
}
