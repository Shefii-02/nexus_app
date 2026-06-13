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

        $query->with('student');
        $query->where('acc_type', 'student');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['batch_id'])) {
            $query->where('batch_id', $filters['batch_id']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")
                ->orWhereHas('student', function ($q) use ($search) {
                    $q->where('roll_number', 'like', "%{$search}%");
                });
        }

        return $query;
    }
}
