<?php

namespace App\Repositories\Payment;

use App\Models\CourseRenewal;
use App\Models\Payment;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class PaymentRepository
{
    public function createAdmission(array $data)
    {
        return Payment::create($data);
    }

    public function createRenewal(array $data)
    {
        return CourseRenewal::create($data);
    }

    public function admissionList()
    {
        return Payment::with(['student.user', 'course'])
            ->latest()
            ->paginate(15);
    }

    public function renewalList()
    {
        return CourseRenewal::with(['student.user', 'course'])
            ->latest()
            ->paginate(15);
    }

    public function transactions()
    {
        $payments = Payment::select(
            'id','student_id','course_id','amount',
            'payment_date as date',
            DB::raw("'admission' as type")
        );

        $renewals = CourseRenewal::select(
            'id','student_id','course_id','amount',
            'renewal_date as date',
            DB::raw("'renewal' as type")
        );

        return $payments->unionAll($renewals)->get();
    }
}
