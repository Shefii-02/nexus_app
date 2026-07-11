<?php

namespace App\Console\Commands;

use App\Services\Cron\ClassReminderService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;


class ClassReminderCommand extends Command
{
    protected $signature = 'class:reminder';

    protected $description = 'Send class reminder notifications';

    public function handle(ClassReminderService $service): int
    {
        $count = $service->sendUpcomingClassReminders();

        $this->info("{$count} reminder(s) sent.");

        return self::SUCCESS;
    }
}
