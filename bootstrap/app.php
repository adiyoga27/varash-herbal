<?php

use App\Http\Middleware\EnsureUserIsActive;
use App\Services\ActivityLogger;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias(['admin' => EnsureUserIsActive::class]);
        $middleware->redirectGuestsTo(fn () => route('admin.login'));
        $middleware->redirectUsersTo(fn () => route('admin.dashboard'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // Catat error server (500) ke activity log agar terlihat di panel admin
        $exceptions->report(function (Throwable $e) {
            ActivityLogger::log('exception', class_basename($e).': '.$e->getMessage(), null, [
                'file' => $e->getFile().':'.$e->getLine(),
            ], 'error', 'error');
        });
    })->create();
