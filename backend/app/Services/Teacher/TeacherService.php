<?php

namespace App\Services\Teacher;

use App\DTOs\TeacherDTO;
use App\Models\User;
use App\Repositories\Teacher\TeacherRepositoryInterface;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class TeacherService extends BaseService
{

    public function __construct(TeacherRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Create a new teacher
     */
    public function create(TeacherDTO $dto): object
    {
        return DB::transaction(function () use ($dto) {

            // ✅ 1. Create User
            $user = User::create($dto->toUserArray());

            // ✅ 2. Assign Role (if using spatie)
            $user->assignRole('teacher');

            // ✅ 3. Create Teacher
            $teacher = $this->repository->create(
                $dto->toTeacherArray($user->id)
            );

            return $teacher;
        });
    }

    /**
     * Update teacher
     */
    public function update(int $id, TeacherDTO $dto): object
    {
        return DB::transaction(function () use ($id, $dto) {


            $user = $this->repository->find($id);

            if (!$user) {
                throw new \Exception('Teacher not found');
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
                $user->update($userData);
            }

            // 🔹 2. Update TEACHER TABLE
            $teacherData = array_filter([
                'subject' => $dto->subject,
                'phone' => $dto->phone,
                'qualification' => $dto->qualification,
                'address' => $dto->address,
                'experience_years' => $dto->experience_years,
                'status' => $dto->status,
            ], fn($v) => $v !== null);

            if (!empty($teacherData)) {
                $user->teacher()->updateOrCreate(
                    [
                        'user_id' => $user->id,
                    ],
                    $teacherData
                );
                // $user->teacher->update($teacherData);
            }

            return $user->fresh()->load('teacher');
        });
    }

    /**
     * Get teacher by user ID
     */
    public function getByUserId(int $userId): ?object
    {
        return $this->repository->findByUserId($userId);
    }

    /**
     * Get active teachers with pagination
     */
    public function getActive(int $page = 1, int $perPage = 15)
    {
        return $this->repository->getActiveTeachers();
    }

    /**
     * Get teacher with courses
     */
    public function getWithCourses(int $id)
    {
        return $this->repository->findWithCourses($id);
    }

    /**
     * Delete teacher
     */
    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {

            $teacher = $this->repository->find($id);

            if (!$teacher) {
                throw new \Exception('Teacher not found');
            }

            // 🔹 1. Soft delete USER
            if ($teacher->user) {
                $teacher->user->delete(); // ✅ soft delete (uses SoftDeletes)
            }

            // 🔹 2. Delete TEACHER
            $teacher->delete(); // can be hard or soft depending on model

            return true;
        });
    }
}
