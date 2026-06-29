<?php

namespace App\Repositories\TeacherPayment;

use App\DTO\TeacherPaymentDTO;
use App\Models\TeacherPayment;
use Illuminate\Pagination\LengthAwarePaginator;

class TeacherPaymentRepository
{
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        return TeacherPayment::query()
            ->with('teacher')
            ->when(
                !empty($filters['search']),
                fn($q) => $q->whereHas(
                    'teacher',
                    fn($q) => $q->where('name', 'like', "%{$filters['search']}%")
                )
            )
            ->when(
                !empty($filters['status']),
                fn($q) => $q->where('status', $filters['status'])
            )
            ->when(
                !empty($filters['teacher_id']),
                fn($q) => $q->where('teacher_id', $filters['teacher_id'])
            )
            ->latest()
            ->paginate($filters['per_page'] ?? 15);
    }

    public function findOrFail(int $id): TeacherPayment
    {
        return TeacherPayment::with('teacher')->findOrFail($id);
    }

    public function create(TeacherPaymentDTO $dto, int $createdBy): TeacherPayment
    {
        return TeacherPayment::create([
            ...$dto->toArray(),
            'created_by' => $createdBy,
        ]);
    }

    public function update(TeacherPayment $payment, TeacherPaymentDTO $dto): TeacherPayment
    {
        $payment->update($dto->toArray());
        return $payment->fresh('teacher');
    }

    public function release(TeacherPayment $payment, int $releasedBy): TeacherPayment
    {
        $payment->update([
            'status'      => 'released',
            'released_by' => $releasedBy,
            'paid_at'     => now(),
        ]);
        return $payment->fresh('teacher');
    }

    public function delete(TeacherPayment $payment): void
    {
        $payment->delete();
    }
}
