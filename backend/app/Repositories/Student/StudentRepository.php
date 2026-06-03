<?php

namespace App\Repositories\Student;

use App\Models\Student;
use App\Repositories\BaseRepository;

class StudentRepository extends BaseRepository implements StudentRepositoryInterface
{
    public function __construct(Student $student)
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
            ->with(['user', 'batch'])
            ->paginate(15);
    }

    public function getActiveStudents()
    {
        return $this->model->active()->with(['user', 'batch'])->paginate(15);
    }

    protected function applyFilters($query, array $filters)
    {
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['batch_id'])) {
            $query->where('batch_id', $filters['batch_id']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('roll_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
        }

        return $query;
    }
}
