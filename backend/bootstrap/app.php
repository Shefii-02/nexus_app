<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web:      __DIR__ . '/../routes/web.php',
        api:      __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health:   '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            \App\Http\Middleware\ForceJsonResponse::class,
        ]);
    })
    ->withBroadcasting(
        __DIR__ . '/../routes/channels.php',
        [
            // 'prefix'     => 'broadcasting',
            ['prefix' => 'api/broadcasting', 'middleware' => ['auth:api']],

            'middleware' => ['auth:api'], // ← JWT guard
        ]
    )
    // ->withSchedule(function ($schedule) {
    //     $schedule->command('renewal:create')
    //         ->dailyAt('00:30');

    //     $schedule->command('renewal:expire')
    //         ->everyTenMinutes();

    //     $schedule->command('admission:suspend')
    //         ->everyFiveMinutes();

    //     $schedule->command('class:reminder')
    //         ->everyMinute();
    // })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $e, $request) {
            return response()->json([
                'status'  => false,
                'message' => 'Login expired. Please login again.',
            ], 401);
        });
    })->create();



