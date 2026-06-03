<?php

namespace App\Repositories\StaffPayment;

use App\Models\StaffPayment;

class StaffPaymentRepository
implements StaffPaymentRepositoryInterface
{
    public function all(
        array $filters = []
    ) {

        $query = StaffPayment::query()
            ->with([
                'staff',
                'releaser'
            ]);

        if (
            !empty($filters['staff_id'])
        ) {
            $query->where(
                'staff_id',
                $filters['staff_id']
            );
        }

        if (
            !empty($filters['month'])
        ) {
            $query->where(
                'month',
                $filters['month']
            );
        }

        if (
            !empty($filters['status'])
        ) {
            $query->where(
                'status',
                $filters['status']
            );
        }

        return $query
            ->latest()
            ->paginate(
                request('per_page', 20)
            );
    }

    public function pending()
    {
        return StaffPayment::query()

            ->with([
                'staff'
            ])

            ->where(
                'status',
                'pending'
            )

            ->latest()

            ->paginate(
                request('per_page', 20)
            );
    }

    public function history()
    {
        return StaffPayment::query()

            ->with([
                'staff',
                'releaser'
            ])

            ->where(
                'status',
                'released'
            )

            ->latest()

            ->paginate(
                request('per_page', 20)
            );
    }

    public function find(
        int $id
    ) {
        return StaffPayment::query()

            ->with([
                'staff',
                'releaser'
            ])

            ->findOrFail(
                $id
            );
    }

    public function create(
        array $data
    ) {
        return StaffPayment::create(
            $data
        );
    }

    public function update(
        int $id,
        array $data
    ) {

        $payment =
            StaffPayment::findOrFail(
                $id
            );

        $payment->update(
            $data
        );

        return $payment->fresh([
            'staff',
            'releaser'
        ]);
    }

    public function delete(
        int $id
    ) {

        $payment =
            StaffPayment::findOrFail(
                $id
            );

        return $payment->delete();
    }
}
