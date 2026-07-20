<?php

namespace App\Services\Staff;

use App\DTOs\StaffDTO;
use App\Models\Staff;
use App\Models\User;
use App\Repositories\Staff\StaffRepositoryInterface;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class StaffService extends BaseService
{
    public function __construct(StaffRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function create(StaffDTO $dto): object
    {
        return DB::transaction(function () use ($dto) {

            // ✅ 1. Create User
            $user = User::create($dto->toUserArray());

            // ✅ 2. Assign Role (if using spatie)
            $user->assignRole('staff');

            // ✅ 3. Create Staff
            $staff = Staff::create(
                $dto->toStaffArray($user->id)
            );

            return $staff;
        });
    }

    public function update(int $id, StaffDTO $dto): object
    {
        return DB::transaction(function () use ($id, $dto) {

            $staff = $this->repository->find($id);

            if (!$staff) {
                throw new \Exception('Staff not found');
            }

            // 🔹 1. Update USER TABLE
            $userData = array_filter([
                'name' => $dto->name,
                'email' => $dto->email,
                'phone' => $dto->phone,
                'status' => $dto->status,
                'password' => $dto->password ? bcrypt($dto->password) : null,
            ], fn($v) => $v !== null);

            if (!empty($userData)) {
                $staff->update($userData);
            }

            // 🔹 2. Update STAFF TABLE
            $staffData = array_filter([
                'department' => $dto->department,
                'designation' => $dto->designation,
                'phone' => $dto->phone,
                'address' => $dto->address,
                'status' => $dto->status,
            ], fn($v) => $v !== null);

            if (!empty($staffData)) {
                $staff->staff()->updateOrCreate(
                    [
                        'user_id' => $staff->id,
                    ],
                    $staffData
                );
            }

            return $staff->fresh()->load('staff');
        });
    }

    public function getByUserId(int $userId): ?object
    {
        return $this->repository->findByUserId($userId);
    }

    public function getByDepartment(string $department, int $page = 1, int $perPage = 15)
    {
        return $this->repository->getByDepartment($department);
    }

    public function getActive(int $page = 1, int $perPage = 15)
    {
        return $this->repository->getActiveStaff();
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            return $this->repository->delete($id);
        });
    }

    public function forceDelete(int $id): bool
    {
        return User::withTrashed()->findOrFail($id)->forceDelete(); // permanent delete
    }
}
