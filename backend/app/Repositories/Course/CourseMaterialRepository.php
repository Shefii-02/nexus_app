<?php

namespace App\Repositories\Course;

use App\Models\CourseMaterial;
use App\Repositories\BaseRepository;

class CourseMaterialRepository extends BaseRepository implements CourseMaterialRepositoryInterface
{
    public function __construct(CourseMaterial $model)
    {
        parent::__construct($model);
    }

    public function getByCourse(int $courseId, array $filters = [])
    {
        $query = $this->model
            ->where('course_id', $courseId)
            ->whereNull('deleted_at')
            ->orderBy('order');

        return $this->applyFilters($query, $filters)->get();
    }

    public function reorder(int $courseId, array $orderedIds): bool
    {
        foreach ($orderedIds as $position => $id) {
            $this->model
                ->where('id', $id)
                ->where('course_id', $courseId)
                ->update(['order' => $position + 1]);
        }
        return true;
    }

    protected function applyFilters($query, array $filters)
    {
        if (!empty($filters['material_type'])) {
            $query->where('material_type', $filters['material_type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query;
    }
}
