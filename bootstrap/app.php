<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\DiscordAlerts\Facades\DiscordAlert;
use Spatie\DiscordAlerts\DiscordAlert as DiscordAlertClass;
use Illuminate\Support\Facades\Auth;
use BezhanSalleh\FilamentExceptions\FilamentExceptions;




return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->reportable(function (Exception|Throwable $e) {
            FilamentExceptions::report($e);
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->report(function (Throwable $e) {
            DiscordAlert::message(
                "🚨 An Exception Occurred!",
                [
                    [
                        'title' => 'Exception Report',
                        'description' => $e->getMessage(),
                        'color' => '#FF0000',
                        'fields' => [
                            [
                                'name' => 'URL',
                                'value' => request()->fullUrl(),
                            ],
                            [
                                'name' => 'File',
                                'value' => $e->getFile() . ':' . $e->getLine(),
                            ],
                            [
                                'name' => 'IP Address',
                                'value' => request()->ip(),
                            ],
                            [
                                'name' => 'User',
                                'value' => Auth::check() ? (Auth::user()->id . ' (' . Auth::user()->email . ')') : 'Guest',
                            ],
                            [
                                'name' => 'Exception Type',
                                'value' => get_class($e),
                            ],
                            [
                                'name' => 'Request Method',
                                'value' => request()->method(),
                            ],
                            [
                                'name' => 'User Agent',
                                'value' => request()->userAgent(),
                            ],
                            [
                                'name' => 'Environment',
                                'value' => app()->environment(),
                            ],
                            [
                                'name' => 'Stack Trace',
                                'value' => implode("\n", array_slice(explode("\n", $e->getTraceAsString()), 0, 5)),
                            ],
                            [
                                'name' => 'Request Input',
                                'value' => json_encode(request()->except(['password', 'token', '_token']), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                            ],
                        ],
                        'timestamp' => now()->toIso8601String(),
                    ]
                ]
            );
        });


    })->create();
