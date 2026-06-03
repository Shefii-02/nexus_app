<?php

namespace App\Services\CourseClass;

use App\DTOs\CourseClassDTO;
use App\Repositories\CourseClass\CourseClassRepositoryInterface;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CourseClassService extends BaseService
{
    public function __construct(CourseClassRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function create(CourseClassDTO $dto): object
    {
        return DB::transaction(function () use ($dto) {
            return $this->repository->create($dto->toArray());
        });
    }

    public function update(int $id, CourseClassDTO $dto): bool
    {
        return DB::transaction(function () use ($id, $dto) {
            return $this->repository->update($id, $dto->toArray());
        });
    }

    public function getByCourse(int $courseId)
    {
        return $this->repository->getByCourse($courseId);
    }

    public function getByTeacher(int $teacherId)
    {
        return $this->repository->getByTeacher($teacherId);
    }

    public function getUpcoming()
    {
        return $this->repository->getUpcoming();
    }

    public function getByDateRange($startDate, $endDate)
    {
        return $this->repository->getByDateRange($startDate, $endDate);
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            return $this->repository->delete($id);
        });
    }
}
