<?php

namespace App\Services\TeacherPayment;

use App\DTOs\TeacherPaymentDTO;
use App\Models\TeacherPayment;
use App\Repositories\TeacherPayment\TeacherPaymentRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class TeacherPaymentService
{
    public function __construct(
        private readonly TeacherPaymentRepository $repo
    ) {}

    public function list(array $filters): LengthAwarePaginator
    {
        return $this->repo->paginate($filters);
    }

    public function find(int $id): TeacherPayment
    {
        return $this->repo->findOrFail($id);
    }

    public function create(TeacherPaymentDTO $dto): TeacherPayment
    {
        return $this->repo->create($dto, Auth::id());
    }

    public function update(int $id, TeacherPaymentDTO $dto): TeacherPayment
    {
        $payment = $this->repo->findOrFail($id);
        return $this->repo->update($payment, $dto);
    }

    public function release(int $id): TeacherPayment
    {
        $payment = $this->repo->findOrFail($id);

        if ($payment->status === 'released') {
            abort(422, 'Payment already released.');
        }

        return $this->repo->release($payment, Auth::id());
    }

    public function delete(int $id): void
    {
        $payment = $this->repo->findOrFail($id);
        $this->repo->delete($payment);
    }
}
