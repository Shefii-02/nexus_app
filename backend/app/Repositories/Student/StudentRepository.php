<?php

namespace App\Repositories\Student;

use App\Models\User;
use App\Repositories\BaseRepository;

class StudentRepository extends BaseRepository implements StudentRepositoryInterface
{
    public function __construct(User $student)
    {
        parent::__construct($student);
    }

    public function findByUserId(int $userId): ?object
    {
        return $this->model->where('user_id', $userId)->first();
    }

    public function findByRollNumber(string $rollNumber): ?object
    {
        return $this->model->where('roll_number', $rollNumber)->first();
    }

    public function getByBatch(int $batchId)
    {
        return $this->model->where('batch_id', $batchId)
            ->with(['student', 'batch'])
            ->paginate(15);
    }

    public function getActiveStudents()
    {
        return $this->model->active()->with(['student', 'batch'])->paginate(15);
    }

    protected function applyFilters($query, array $filters)
    {
        $query->with('student')
            ->where('acc_type', 'student');

        // Status Filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }


        // Search Filter
        if (!empty($filters['search'])) {
            $search = trim($filters['search']);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%")
                    ->orWhereHas('student', function ($teacher) use ($search) {
                        $teacher->where('roll_number', 'LIKE', "%{$search}%");
                    });
            });
        }

        return $query;
    }
}
