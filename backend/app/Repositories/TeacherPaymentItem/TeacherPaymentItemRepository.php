<?php

namespace App\Repositories\TeacherPaymentItem;

use App\Models\TeacherPaymentItem;

class TeacherPaymentItemRepository
implements TeacherPaymentItemRepositoryInterface
{
    public function all(
        array $filters = []
    ) {

        return TeacherPaymentItem::with([
            'teacher',
            'course'
        ])
        ->latest()
        ->paginate(20);
    }

    public function pending()
    {
        return TeacherPaymentItem::with([
            'teacher',
            'course'
        ])
        ->where(
            'status',
            'pending'
        )
        ->paginate(20);
    }

    public function find(
        int $id
    ) {
        return TeacherPaymentItem::findOrFail(
            $id
        );
    }

    public function create(
        array $data
    ) {
        return TeacherPaymentItem::create(
            $data
        );
    }

    public function update(
        int $id,
        array $data
    ) {
        $item =
            TeacherPaymentItem::findOrFail($id);

        $item->update($data);

        return $item;
    }

    public function pendingByTeacher(
    int $teacherId
)
{
    return TeacherPaymentItem::query()

        ->with([
            'teacher',
            'course'
        ])

        ->where(
            'teacher_id',
            $teacherId
        )

        ->where(
            'status',
            'pending'
        )

        ->latest()

        ->get();
}
}
