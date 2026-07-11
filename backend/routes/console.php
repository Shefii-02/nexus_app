<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');



/*
|--------------------------------------------------------------------------
| Scheduled Commands
|--------------------------------------------------------------------------
*/

Schedule::command('renewal:create')
    ->dailyAt('00:30')
    ->withoutOverlapping();

Schedule::command('renewal:expire')
    ->everyTenMinutes()
    ->withoutOverlapping();

Schedule::command('admission:suspend')
    ->everyFiveMinutes()
    ->withoutOverlapping();

Schedule::command('class:reminder')
    ->everyMinute()
    ->withoutOverlapping();
