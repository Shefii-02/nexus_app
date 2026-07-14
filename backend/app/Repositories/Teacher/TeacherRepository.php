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
        $query->with('teacher');
        $query->where('acc_type', 'teacher');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['subject'])) {

            $subject = $filters['subject'];
            $query->whereHas('teacher', function ($q) use ($subject) {
                $q->where('subject', 'like', "%{$subject}%");
            });
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('teacher', function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%");
            })
                ->orWhere('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
        }

        return $query;
    }
}
