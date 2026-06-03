<?php

namespace App\Repositories\AdmissionRenewal;

use App\Models\AdmissionRenewal;

class AdmissionRenewalRepository
implements AdmissionRenewalRepositoryInterface
{
    public function all(array $filters = [])
    {
        return AdmissionRenewal::with([
            'student',
            'course',
            'admission'
        ])
        ->latest()
        ->paginate(
            $filters['per_page'] ?? 15
        );
    }

    public function due()
    {
        return AdmissionRenewal::with([
            'student',
            'course'
        ])
        ->where(
            'status',
            'pending'
        )
        ->paginate(20);
    }

    public function find(int $id)
    {
        return AdmissionRenewal::with([
            'student',
            'course',
            'admission'
        ])
        ->findOrFail($id);
    }

    public function create(array $data)
    {
        return AdmissionRenewal::create($data);
    }

    public function update(
        int $id,
        array $data
    ) {
        $renewal =
            AdmissionRenewal::findOrFail($id);

        $renewal->update($data);

        return $renewal;
    }

    public function delete(int $id)
    {
        return AdmissionRenewal::destroy($id);
    }
}
