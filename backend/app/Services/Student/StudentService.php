<?php

namespace App\Services\Student;

use App\DTOs\StudentDTO;
use App\Models\Student;
use App\Models\User;
use App\Repositories\Student\StudentRepositoryInterface;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentService extends BaseService
{
    public function __construct(StudentRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function create(StudentDTO $dto): object
    {
        // Log::info($dto->toUserArray());
        return DB::transaction(function () use ($dto) {
            // ✅ 1. Create User
            $user = User::create($dto->toUserArray());


            // ✅ 2. Assign Role (if using spatie)
            $user->assignRole('student');

            // ✅ 3. Create Student
            $student = Student::create(
                $dto->toStudentArray($user->id)
            );
            return $student;
        });
    }

    public function update(int $id, StudentDTO $dto): object
    {
        return DB::transaction(function () use ($id, $dto) {
            $student = $this->repository->find($id);

            if (!$student) {
                throw new \Exception('Student not found');
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
                $student->update($userData);
            }

            // 🔹 2. Update STUDENT TABLE
            $studentData = array_filter([
                'roll_number' => $dto->roll_number,
                'phone' => $dto->phone,
                'guardian_name' => $dto->guardian_name,
                'guardian_phone' => $dto->guardian_phone,
                'status' => $dto->status,
                'address' => $dto->address,
            ], fn($v) => $v !== null);

            if (!empty($studentData)) {
                $student->student()->updateOrCreate(
                    [
                        'user_id' => $student->id,
                    ],
                    $studentData
                );
            }

            return $student->fresh()->load('student');
        });
    }

    public function getByUserId(int $userId): ?object
    {
        return $this->repository->findByUserId($userId);
    }

    public function getByRollNumber(string $rollNumber): ?object
    {
        return $this->repository->findByRollNumber($rollNumber);
    }

    public function getByBatch(int $batchId, int $page = 1, int $perPage = 15)
    {
        return $this->repository->getByBatch($batchId);
    }

    public function getActive(int $page = 1, int $perPage = 15)
    {
        return $this->repository->getActiveStudents();
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
