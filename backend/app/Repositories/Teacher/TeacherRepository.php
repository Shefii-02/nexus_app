<?php

namespace App\Repositories\Teacher;

use App\Models\Teacher;
use App\Repositories\BaseRepository;

class TeacherRepository extends BaseRepository implements TeacherRepositoryInterface
{
    public function __construct(Teacher $teacher)
    {
        parent::__construct($teacher);
    }

    public function findByUserId(int $userId): ?object
    {
        return $this->model->where('user_id', $userId)->first();
    }

    public function getActiveTeachers()
    {
        return $this->model->active()->with('user')->paginate(15);
    }

    public function findWithCourses(int $id)
    {
        return $this->model->with(['user', 'courses'])->find($id);
    }

    protected function applyFilters($query, array $filters)
    {
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['subject'])) {
            $query->where('subject', $filters['subject']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('subject', 'like', "%{$search}%");
        }

        return $query;
    }
}
