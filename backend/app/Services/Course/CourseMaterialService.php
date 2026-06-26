<?php

namespace App\Services\Course;

use App\Repositories\Course\CourseMaterialRepositoryInterface;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CourseMaterialService extends BaseService
{
    public function __construct(CourseMaterialRepositoryInterface $repository)
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
        return DB::transaction(function () use ($id) {
            $material = $this->repository->find($id);

            // Remove file from storage if it's a local path
            if ($material && $material->file_url && !str_starts_with($material->file_url, 'http')) {
                Storage::disk('public')->delete($material->file_url);
            }

            return $this->repository->delete($id);
        });
    }

    public function getByCourse(int $courseId, array $filters = [])
    {
        return $this->repository->getByCourse($courseId, $filters);
    }

    public function reorder(int $courseId, array $orderedIds): bool
    {
        return $this->repository->reorder($courseId, $orderedIds);
    }
}
