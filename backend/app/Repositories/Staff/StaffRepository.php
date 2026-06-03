<?php

namespace App\Repositories\Staff;

use App\Models\Staff;
use App\Repositories\BaseRepository;

class StaffRepository extends BaseRepository implements StaffRepositoryInterface
{
    public function __construct(Staff $staff)
    {
        parent::__construct($staff);
    }

    public function findByUserId(int $userId): ?object
    {
        return $this->model->where('user_id', $userId)->first();
    }

    public function getByDepartment(string $department)
    {
        return $this->model->where('department', $department)
            ->with('user')
            ->paginate(15);
    }

    public function getActiveStaff()
    {
        return $this->model->active()->with('user')->paginate(15);
    }

    protected function applyFilters($query, array $filters)
    {
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['department'])) {
            $query->where('department', $filters['department']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('designation', 'like', "%{$search}%");
        }

        return $query;
    }
}
