<?php

namespace App\Repositories\CourseClass;

use App\Models\CourseClass;
use App\Models\LeaveRequest;
use App\Repositories\BaseRepository;

class LeaveRequestRepository
implements LeaveRequestRepositoryInterface
{
    public function all(
        array $filters = []
    )
    {
        return LeaveRequest::query()

            ->with([
                'user',
                'approver'
            ])

            ->latest()

            ->paginate(
                request(
                    'per_page',
                    20
                )
            );
    }

    public function find(
        int $id
    )
    {
        return LeaveRequest::with([
            'user',
            'approver'
        ])->findOrFail($id);
    }

    public function create(
        array $data
    )
    {
        return LeaveRequest::create(
            $data
        );
    }

    public function update(
        int $id,
        array $data
    )
    {
        $leave =
            LeaveRequest::findOrFail(
                $id
            );

        $leave->update(
            $data
        );

        return $leave;
    }

    public function delete(
        int $id
    )
    {
        return LeaveRequest::findOrFail(
            $id
        )->delete();
    }
}
