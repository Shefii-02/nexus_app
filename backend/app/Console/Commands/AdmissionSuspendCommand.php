<?php

namespace App\Console\Commands;

use App\Services\Cron\AdmissionService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

class AdmissionSuspendCommand extends Command
{
   protected $signature = 'admission:suspend';

    protected $description = 'Suspend admissions after course end date';

    public function handle(AdmissionService $service): int
    {
        $count = $service->suspendExpiredAdmissions();

        $this->info("{$count} admission(s) suspended.");

        return self::SUCCESS;
    }
}
