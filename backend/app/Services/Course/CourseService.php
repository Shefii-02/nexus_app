<?php

namespace App\Services\Course;

use App\DTOs\CourseDTO;
use App\Repositories\Course\CourseRepositoryInterface;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class CourseService extends BaseService
{
    public function __construct(CourseRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function create(CourseDTO $dto): object
    {
        return DB::transaction(function () use ($dto) {
            return $this->repository->create($dto->toArray());
        });
    }

    public function update(int $id, CourseDTO $dto): bool
    {
        return DB::transaction(function () use ($id, $dto) {
            return $this->repository->update($id, $dto->toArray());
        });
    }

    public function getByCode(string $code): ?object
    {
        return $this->repository->findByCode($code);
    }

    public function getByTeacher(int $teacherId, int $page = 1, int $perPage = 15)
    {
        return $this->repository->getByTeacher($teacherId);
    }

    public function getByBatch(int $batchId, int $page = 1, int $perPage = 15)
    {
        return $this->repository->getByBatch($batchId);
    }

    public function getActive(int $page = 1, int $perPage = 15)
    {
        return $this->repository->getActiveCourses();
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            return $this->repository->delete($id);
        });
    }

    public function attachStudent(int $courseId, int $studentId): bool
    {
        $course = $this->repository->find($courseId);
        if (!$course) {
            return false;
        }

        $course->students()->syncWithoutDetaching($studentId);
        return true;
    }

    public function detachStudent(int $courseId, int $studentId): bool
    {
        $course = $this->repository->find($courseId);
        if (!$course) {
            return false;
        }

        $course->students()->detach($studentId);
        return true;
    }
}
