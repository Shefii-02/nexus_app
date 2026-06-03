<?php

namespace App\Repositories\Course;

use App\Models\Course;
use App\Repositories\BaseRepository;

class CourseRepository extends BaseRepository implements CourseRepositoryInterface
{
    public function __construct(Course $course)
    {
        parent::__construct($course);
    }

    public function findByCode(string $code): ?object
    {
        return $this->model->where('code', $code)->first();
    }

    public function getByTeacher(int $teacherId)
    {
        return $this->model->where('teacher_id', $teacherId)
            ->with(['teacher.user', 'batch'])
            ->paginate(15);
    }

    public function getByBatch(int $batchId)
    {
        return $this->model->where('batch_id', $batchId)
            ->with(['teacher.user', 'batch'])
            ->paginate(15);
    }

    public function getActiveCourses()
    {
        return $this->model->active()
            ->with(['teacher.user', 'batch'])
            ->paginate(15);
    }

    protected function applyFilters($query, array $filters)
    {
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['teacher_id'])) {
            $query->where('teacher_id', $filters['teacher_id']);
        }

        if (!empty($filters['batch_id'])) {
            $query->where('batch_id', $filters['batch_id']);
        }

        if (!empty($filters['fee_type'])) {
            $query->where('fee_type', $filters['fee_type']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
        }

        return $query;
    }
}
