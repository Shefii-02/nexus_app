<?php

namespace App\Services\TeacherPaymentItem;

use App\DTOs\TeacherPaymentItemDTO;
use App\Repositories\TeacherPaymentItem\TeacherPaymentItemRepositoryInterface;
use Illuminate\Support\Facades\DB;

class TeacherPaymentItemService
{
    public function __construct(
        private TeacherPaymentItemRepositoryInterface $repository
    ) {}

    public function all(
        array $filters = []
    ) {
        return $this->repository->all(
            $filters
        );
    }

    public function pending()
    {
        return $this->repository->pending();
    }

    public function find(
        int $id
    ) {
        return $this->repository->find(
            $id
        );
    }

    public function create(
        TeacherPaymentItemDTO $dto
    ) {
        return DB::transaction(
            function () use ($dto) {

                return $this->repository
                    ->create(
                        $dto->toArray()
                    );
            }
        );
    }

    public function update(
        int $id,
        TeacherPaymentItemDTO $dto
    ) {
        return DB::transaction(
            function () use (
                $id,
                $dto
            ) {

                return $this->repository
                    ->update(
                        $id,
                        $dto->toArray()
                    );
            }
        );
    }

    public function delete(
        int $id
    ): bool {

        $item =
            $this->repository
            ->find($id);

        if (
            $item->status === 'released'
        ) {
            throw new \Exception(
                'Released payment cannot be deleted'
            );
        }

        return $item->delete();
    }

    public function markReleased(
        array $ids
    ): bool {

        return DB::transaction(
            function () use ($ids) {

                return \App\Models\TeacherPaymentItem::query()
                    ->whereIn(
                        'id',
                        $ids
                    )
                    ->update([
                        'status' => 'released'
                    ]);
            }
        );
    }


    public function pendingByTeacher(
        int $teacherId
    ) {
        return $this->repository
            ->pendingByTeacher(
                $teacherId
            );
    }
}
