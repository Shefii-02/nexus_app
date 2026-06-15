<?php

namespace App\Repositories\Admission;

use App\Models\Admission;

class AdmissionRepository implements AdmissionRepositoryInterface
{
    protected array $defaultRelations = [
        'student',
        'course',
        'teacher',
        'payments',
    ];

    public function all(array $filters = [])
    {
        return Admission::query()
            ->with($this->defaultRelations)
            ->when(!empty($filters['search']), function ($q) use ($filters) {
                $q->whereHas('student', fn($s) =>
                    $s->where('name', 'like', "%{$filters['search']}%")
                );
            })
            ->latest()
            ->paginate($filters['per_page'] ?? 15);
    }

    public function find(int $id)
    {
        return Admission::with($this->defaultRelations)->find($id);
    }

    public function findWithRelations(int $id, array $relations = [])
    {
        return Admission::with($relations)->find($id);
    }

    public function create(array $data)
    {
        return Admission::create($data);
    }

    public function update(int $id, array $data)
    {
        $admission = Admission::findOrFail($id);
        $admission->update($data);
        return $admission->fresh($this->defaultRelations);
    }

    public function delete(int $id): bool
    {
        $admission = Admission::find($id);

        if (!$admission) {
            return false;
        }

        return $admission->delete();
    }

    public function exists(int $id): bool
    {
        return Admission::where('id', $id)->exists();
    }
}
