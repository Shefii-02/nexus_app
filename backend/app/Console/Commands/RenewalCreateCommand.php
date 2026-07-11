<?php

namespace App\Console\Commands;

use App\Services\Cron\RenewalService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;


class RenewalCreateCommand extends Command
{
  protected $signature = 'renewal:create';

    protected $description = 'Create renewal records 5 days before expiry';

    public function handle(RenewalService $service): int
    {
        $count = $service->createUpcomingRenewals();

        $this->info("{$count} renewal(s) created.");

        return self::SUCCESS;
    }
}
