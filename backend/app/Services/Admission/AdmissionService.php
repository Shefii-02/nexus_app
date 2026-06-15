<?php

namespace App\Services\Admission;

use App\DTOs\AdmissionDTO;
use App\Models\AdmissionPayment;
use App\Models\Course;
use App\Models\Transaction;
use App\Repositories\Admission\AdmissionRepositoryInterface;
use Illuminate\Support\Facades\DB;

class AdmissionService
{

    public function all(array $filters = [])
    {
        return $this->repository->all($filters);
    }

    public function __construct(
        protected AdmissionRepositoryInterface $repository
    ) {}

    public function list(array $filters = [])
    {
        return $this->repository->all($filters);
    }

    public function find(int $id)
    {
        return $this->repository->find($id);
    }

    public function findWithRelations(int $id, array $relations = [])
    {
        return $this->repository->findWithRelations($id, $relations);
    }

    public function create(AdmissionDTO $dto): mixed
    {
        return DB::transaction(function () use ($dto) {

            $course = Course::findOrFail($dto->course_id);

            $data = array_merge($dto->toArray(), [
                'actual_fee'     => $course->actual_price,
                'net_fee'        => $course->net_price,
                'admission_date' => now(),
                'expiry_date'    => $course->ended_at,
            ]);

            $admission = $this->repository->create($data);

            if ($dto->paid_amount > 0) {
                $payment = AdmissionPayment::create([
                    'admission_id'   => $admission->id,
                    'student_id'     => $dto->student_id,
                    'course_id'      => $dto->course_id,
                    'amount'         => $dto->paid_amount,
                    'payment_method' => $dto->payment_method,
                    'transaction_no' => $dto->transaction_no,
                    'remarks'        => $dto->remarks,
                    'paid_at'        => now(),
                    'received_by'    => auth()->id(),
                ]);

                Transaction::create([
                    'type'             => 'income',
                    'category'         => 'admission_fee',
                    'reference_type'   => 'admission_payment',
                    'reference_id'     => $payment->id,
                    'amount'           => $dto->paid_amount,
                    'description'      => 'Admission Fee',
                    'transaction_date' => now(),
                    'created_by'       => auth()->id(),
                ]);
            }

            return $admission->load(['student', 'course', 'teacher', 'payments']);
        });
    }

    public function update(int $id, AdmissionDTO $dto): mixed
    {
        return DB::transaction(function () use ($id, $dto) {

            $course = Course::findOrFail($dto->course_id);

            $data = array_merge($dto->toArray(), [
                'actual_fee'  => $course->actual_price,
                'net_fee'     => $course->net_price,
                'expiry_date' => $course->ended_at,
            ]);

            return $this->repository->update($id, $data);
        });
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function payments(int $admissionId): mixed
    {
        return AdmissionPayment::where('admission_id', $admissionId)
            ->latest()
            ->get();
    }
}
