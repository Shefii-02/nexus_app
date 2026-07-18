<?php

namespace App\Repositories\Teacher;

use App\Models\User;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Log;

class TeacherRepository extends BaseRepository implements TeacherRepositoryInterface
{
    public function __construct(User $teacher)
    {
        parent::__construct($teacher);
    }


    public function findByUserId(int $userId): ?object
    {
        return $this->model->where('id', $userId)->first();
    }

    public function getActiveTeachers()
    {
        return $this->model->active()->with('user')->paginate(15);
    }

    public function findWithCourses(int $id)
    {
        return $this->model->with(['teachers', 'courses'])->find($id);
    }

    protected function applyFilters($query, array $filters)
    {
        $query->with('teacher')
            ->where('acc_type', 'teacher');

        // Status Filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Subject Filter
        if (!empty($filters['subject'])) {
            $subject = trim($filters['subject']);

            $query->whereHas('teacher', function ($q) use ($subject) {
                $q->where('subject', 'LIKE', "%{$subject}%");
            });
        }

        // Search Filter
        if (!empty($filters['search'])) {
            $search = trim($filters['search']);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%")
                    ->orWhereHas('teacher', function ($teacher) use ($search) {
                        $teacher->where('subject', 'LIKE', "%{$search}%");
                    });
            });
        }

        return $query;
    }
}
