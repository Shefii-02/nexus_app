<?php

namespace App\Services\Cron;

use App\Models\Admission;
use App\Models\AdmissionRenewal;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RenewalService
{
    /**
     * Create renewal records 5 days before expiry.
     */
    public function createUpcomingRenewals(): int
    {
        $count = 0;

        $admissions = Admission::with('course')
            ->where('status', 'active')
            ->whereDate('expiry_date', Carbon::today()->addDays(5))
            ->get();

        foreach ($admissions as $admission) {

            if (!$admission->course)
                continue;

            if (
                !$admission->course->is_renewal ||
                $admission->course->fee_type !== 'monthly'
            ) {
                continue;
            }

            $renewalFrom = Carbon::parse($admission->expiry_date)->addDay();
            $renewalTo = Carbon::parse($admission->expiry_date)->addMonth();

            $exists = AdmissionRenewal::where('admission_id', $admission->id)
                ->whereDate('renewal_from', $renewalFrom)
                ->exists();

            if ($exists)
                continue;

            AdmissionRenewal::create([
                'admission_id' => $admission->id,
                'student_id' => $admission->student_id,
                'course_id' => $admission->course_id,

                'current_expiry_date' => $admission->expiry_date,

                'renewal_from' => $renewalFrom,
                'renewal_to' => $renewalTo,

                'amount' => $admission->course->net_price,
                'discount_amount' => 0,
                'final_amount' => $admission->course->net_price,

                'status' => 'pending'
            ]);

            $count++;
        }

        return $count;
    }

    /**
     * Suspend admissions if renewal unpaid.
     */
    public function expireRenewals(): int
    {
        $count = 0;

        DB::transaction(function () use (&$count) {

            $renewals = AdmissionRenewal::where('status', 'pending')
                ->whereDate('renewal_to', '<', today())
                ->get();

            foreach ($renewals as $renewal) {

                $renewal->update([
                    'status' => 'cancelled'
                ]);

                Admission::where('id', $renewal->admission_id)
                    ->update([
                        'status' => 'suspended'
                    ]);

                $count++;
            }
        });

        return $count;
    }
}
