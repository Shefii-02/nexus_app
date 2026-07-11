<?php

namespace App\Services\Cron;

use App\Models\Admission;
use App\Models\Conversation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdmissionService
{
    public function suspendExpiredAdmissions(): int
    {
        $count = 0;

        $admissions = Admission::with('course')
            ->where('status', 'active')
            ->get();

        DB::transaction(function () use ($admissions, &$count) {

            foreach ($admissions as $admission) {

                if (!$admission->course)
                    continue;

                if (
                    Carbon::parse($admission->course->ended_at)
                        ->greaterThan(now())
                ) {
                    continue;
                }

                $admission->update([
                    'status' => 'suspended'
                ]);

                Conversation::where('admission_id', $admission->id)
                    ->update([
                        'status' => 'suspended'
                    ]);

                $count++;
            }

        });

        return $count;
    }
}
