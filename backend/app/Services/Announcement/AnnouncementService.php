<?php

namespace App\Services\Announcement;

use App\DTOs\AnnouncementDTO;
use App\Repositories\Announcement\AnnouncementRepositoryInterface;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Collection;

class AnnouncementService extends BaseService
{
    public function create(AnnouncementDTO $dto)
    {
        return DB::transaction(function () use ($dto) {

            $announcement = $this->repository->create($dto->toArray());

            $this->syncTargets($announcement, $dto);

            return $announcement;
        });
    }

    public function update(int $id, AnnouncementDTO $dto)
    {
        return DB::transaction(function () use ($id, $dto) {

            $announcement = $this->repository->find($id);

            $announcement->update($dto->toArray());

            $this->syncTargets($announcement, $dto);

            return $announcement;
        });
    }

    // private function syncTargets($announcement, $dto)
    // {
    //     $announcement->users()->sync($dto->user_ids);
    //     $announcement->roles()->sync($dto->role_ids);
    //     $announcement->batches()->sync($dto->batch_ids);
    // }

    private function syncTargets(
        $announcement,
        AnnouncementDTO $dto
    ): void {

        $userIds = collect();

        switch ($dto->target_type) {

            case 'all_users':

                $userIds = User::pluck('id');
                break;

            case 'all_students':

                $userIds = User::where(
                    'acc_type',
                    'student'
                )->pluck('id');

                break;

            case 'all_teachers':

                $userIds = User::where(
                    'acc_type',
                    'teacher'
                )->pluck('id');

                break;

            case 'all_staff':

                $userIds = User::where(
                    'acc_type',
                    'staff'
                )->pluck('id');

                break;

            case 'users':

                $userIds = collect(
                    $dto->user_ids
                );

                break;

            case 'roles':

                $userIds = User::whereIn(
                    'role_id',
                    $dto->role_ids
                )->pluck('id');

                break;

            case 'batches':

                $userIds = User::whereIn(
                    'batch_id',
                    $dto->batch_ids
                )->pluck('id');

                break;
        }

        $announcement->users()->sync(
            $userIds->unique()->values()
        );
    }

    public function forUser(int $userId)
    {
        return $this->repository->listForUser($userId);
    }

    public function forAll(array $filter = [])
    {
        return $this->repository->all($filter);
    }

    public function delete(int $id)
    {
        return DB::transaction(function () use ($id) {

            $announcement = $this->repository->find($id);

            return $announcement->delete();
        });
    }
}
