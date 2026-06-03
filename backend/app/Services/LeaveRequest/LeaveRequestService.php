<?php

namespace App\Services\LeaveRequest;

use App\DTOs\LeaveRequestDTO;
use App\Repositories\LeaveRequest\LeaveRequestRepositoryInterface;
use Illuminate\Support\Facades\DB;

class LeaveRequestService
{
    public function __construct(
        private LeaveRequestRepositoryInterface $repository
    ) {}

    public function all(
        array $filters = []
    )
    {
        return $this->repository->all(
            $filters
        );
    }

    public function find(
        int $id
    )
    {
        return $this->repository->find(
            $id
        );
    }

    public function create(
        LeaveRequestDTO $dto
    )
    {
        return DB::transaction(
            fn () =>
            $this->repository->create(
                $dto->toArray()
            )
        );
    }

    public function approve(
        int $id
    )
    {
        return $this->repository->update(
            $id,
            [
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now()
            ]
        );
    }

    public function reject(
        int $id
    )
    {
        return $this->repository->update(
            $id,
            [
                'status' => 'rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now()
            ]
        );
    }

    public function delete(
        int $id
    )
    {
        return $this->repository->delete(
            $id
        );
    }
}
