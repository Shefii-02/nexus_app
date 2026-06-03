<?php

namespace App\Repositories\Admission;

use App\Models\Admission;

class AdmissionRepository implements AdmissionRepositoryInterface
{
    public function all(array $filters = [])
    {
        return Admission::query()
            ->with([
                'student',
                'course',
                'teacher'
            ])
            ->when(
                !empty($filters['search']),
                function ($q) use ($filters) {

                    $search = $filters['search'];

                    $q->whereHas(
                        'student',
                        fn ($s) =>
                        $s->where(
                            'name',
                            'like',
                            "%{$search}%"
                        )
                    );
                }
            )
            ->latest()
            ->paginate(
                $filters['per_page'] ?? 15
            );
    }

    public function find(int $id)
    {
        return Admission::with([
            'student',
            'course',
            'teacher'
        ])->find($id);
    }

    public function create(array $data)
    {
        return Admission::create($data);
    }

    public function update(
        int $id,
        array $data
    ) {
        $admission = Admission::findOrFail($id);

        $admission->update($data);

        return $admission->fresh();
    }

    public function delete(int $id)
    {
        return Admission::destroy($id);
    }

    public function exists(
        int $id
    ): bool {
        return Admission::where(
            'id',
            $id
        )->exists();
    }
}
