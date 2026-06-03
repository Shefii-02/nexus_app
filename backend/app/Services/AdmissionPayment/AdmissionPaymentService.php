<?php

namespace App\Services\AdmissionPayment;

use App\DTOs\AdmissionPaymentDTO;
use App\Models\Admission;
use App\Models\Transaction;
use App\Repositories\AdmissionPayment\AdmissionPaymentRepositoryInterface;
use Illuminate\Support\Facades\DB;

class AdmissionPaymentService
{
    public function __construct(
        private AdmissionPaymentRepositoryInterface $repository
    ) {}

    public function all(
        array $filters = []
    ) {
        return $this->repository->all(
            $filters
        );
    }

    public function find(int $id)
    {
        return $this->repository->find(
            $id
        );
    }

    public function create(
        AdmissionPaymentDTO $dto
    ) {

        return DB::transaction(
            function () use ($dto) {

                $admission =
                    Admission::findOrFail(
                        $dto->admission_id
                    );

                $payment =
                    $this->repository
                        ->create([

                            'admission_id' =>
                                $admission->id,

                            'student_id' =>
                                $admission->student_id,

                            'course_id' =>
                                $admission->course_id,

                            'amount' =>
                                $dto->amount,

                            'payment_method' =>
                                $dto->payment_method,

                            'transaction_no' =>
                                $dto->transaction_no,

                            'remarks' =>
                                $dto->remarks,

                            'paid_at' =>
                                now(),

                            'received_by' =>
                                auth()->id(),
                        ]);

                Transaction::create([

                    'type' => 'income',

                    'category' =>
                        'admission_payment',

                    'reference_type' =>
                        'admission_payment',

                    'reference_id' =>
                        $payment->id,

                    'amount' =>
                        $payment->amount,

                    'transaction_date' =>
                        now(),

                    'created_by' =>
                        auth()->id(),
                ]);

                return $payment;
            }
        );
    }
}
