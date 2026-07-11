<?php

namespace App\Console\Commands;

use App\Services\Cron\RenewalService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;


class RenewalExpireCommand extends Command
{


    protected $signature = 'renewal:expire';

    protected $description = 'Suspend expired admissions with unpaid renewals';

    public function handle(RenewalService $service): int
    {
        $count = $service->expireRenewals();

        $this->info("{$count} admission(s) suspended.");

        return self::SUCCESS;
    }
}
