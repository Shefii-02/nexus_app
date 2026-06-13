<?php

namespace App\Services\Admission;

use App\DTOs\AdmissionDTO;
use App\Models\AdmissionPayment;
use App\Models\Transaction;
use App\Repositories\Admission\AdmissionRepositoryInterface;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class AdmissionService extends BaseService
{
    public function __construct(
        AdmissionRepositoryInterface $repository
    ) {
        parent::__construct($repository);
    }

    public function create(
        AdmissionDTO $dto
    ) {

        return DB::transaction(
            function () use ($dto) {

                /*
                |--------------------------------------------------------------------------
                | Create Admission
                |--------------------------------------------------------------------------
                */

                $data = $dto->toArray();

                if (empty($dto->expiry_date)) {

                    $course = \App\Models\Course::findOrFail(
                        $dto->course_id
                    );

                    $data['expiry_date'] =
                        \Carbon\Carbon::parse(
                            $dto->admission_date
                        )->addDays(
                            $course->duration_days ?? 30
                        )->toDateString();
                }


                $admission =
                    $this->repository
                    ->create(
                        $data
                    );

                /*
                |--------------------------------------------------------------------------
                | Initial Payment
                |--------------------------------------------------------------------------
                */

                if (
                    property_exists(
                        $dto,
                        'paid_amount'
                    ) &&
                    $dto->paid_amount >= 0
                ) {

                    $payment =
                        AdmissionPayment::create([

                            'admission_id' =>
                            $admission->id,

                            'amount' =>
                            $dto->paid_amount,

                            'payment_method' =>
                            $dto->payment_method,

                            'transaction_no' =>
                            $dto->transaction_no,

                            'remarks' =>
                            $dto->remarks,

                            'paid_at' =>
                            now(),

                            'created_by' =>
                            auth()->id(),
                        ]);

                    /*
                    |--------------------------------------------------------------------------
                    | Transaction Ledger
                    |--------------------------------------------------------------------------
                    */

                    Transaction::create([

                        'type' => 'income',

                        'category' =>
                        'admission_fee',

                        'reference_type' =>
                        'admission_payment',

                        'reference_id' =>
                        $payment->id,

                        'amount' =>
                        $dto->paid_amount,

                        'description' =>
                        'Admission payment collected',

                        'transaction_date' =>
                        now(),

                        'created_by' =>
                        auth()->id(),
                    ]);
                }

                return $admission->load([
                    'student',
                    'course',
                    'teacher',
                    'payments'
                ]);
            }
        );
    }

    public function update(
        int $id,
        AdmissionDTO $dto
    ) {

        return DB::transaction(
            function () use (
                $id,
                $dto
            ) {

                $data = $dto->toArray();

                if (empty($dto->expiry_date)) {

                    $course = \App\Models\Course::findOrFail(
                        $dto->course_id
                    );

                    $data['expiry_date'] =
                        \Carbon\Carbon::parse(
                            $dto->admission_date
                        )->addDays(
                            $course->duration_days ?? 30
                        )->toDateString();
                }

                return $this->repository
                    ->update(
                        $id,
                        $data
                    );
            }
        );
    }

    public function delete(
        int $id
    ): bool {

        return DB::transaction(
            function () use ($id) {

                return (bool)
                $this->repository
                    ->delete($id);
            }
        );
    }

    public function all(array $filters = [])
    {
        return $this->repository
            ->all($filters);
    }

    public function find(
        int $id
    ) {
        return $this->repository
            ->find($id);
    }

    public function exists(
        int $id
    ): bool {
        return $this->repository
            ->exists($id);
    }

    public function payments(
        int $admissionId
    ) {
        return AdmissionPayment::where(
            'admission_id',
            $admissionId
        )
            ->latest()
            ->get();
    }
}
