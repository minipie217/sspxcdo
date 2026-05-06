<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Remove redirectGuestsTo entirely — each middleware handles
        // its own redirects explicitly
        $middleware->alias([
            'admin'   => \App\Http\Middleware\RedirectIfNotAdmin::class,
            'sponsor' => \App\Http\Middleware\RedirectIfNotSponsor::class,
            'auth' => \App\Http\Middleware\Authenticate::class,
            'guest' => \Illuminate\Auth\Middleware\RedirectIfAuthenticated::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('tickets:release-expired')->everyMinute();
    })
    ->create();