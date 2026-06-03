<?php

namespace App\Repositories\AdmissionPayment;

use App\Models\AdmissionPayment;

class AdmissionPaymentRepository
implements AdmissionPaymentRepositoryInterface
{
    public function all(
        array $filters = []
    ) {

        return AdmissionPayment::with([
            'student',
            'course',
            'admission'
        ])
        ->latest()
        ->paginate(
            $filters['per_page'] ?? 20
        );
    }

    public function find(int $id)
    {
        return AdmissionPayment::findOrFail($id);
    }

    public function create(array $data)
    {
        return AdmissionPayment::create(
            $data
        );
    }
}
