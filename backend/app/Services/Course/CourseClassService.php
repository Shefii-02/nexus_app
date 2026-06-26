<?php

namespace App\Services\Course;

use App\Repositories\Course\CourseClassRepositoryInterface;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class CourseClassService extends BaseService
{
    public function __construct(CourseClassRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function create(array $data): object
    {
        return DB::transaction(fn() => $this->repository->create($data));
    }

    public function update(int $id, array $data): bool
    {
        return DB::transaction(fn() => $this->repository->update($id, $data));
    }

    public function delete(int $id): bool
    {
        return DB::transaction(fn() => $this->repository->delete($id));
    }

    public function getByCourse(int $courseId, array $filters = [])
    {
        return $this->repository->getByCourse($courseId, $filters);
    }

    public function getToday(int $userId, string $accType)
    {
        return $this->repository->getToday($userId, $accType);
    }
}
