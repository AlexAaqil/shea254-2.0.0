<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'super_admin' => \App\Http\Middleware\SuperAdminMiddleware::class,
            'active' => \App\Http\Middleware\ActiveMiddleware::class,
        ]);

        $middleware->group('authenticated_user', [
            'auth',
            'active',
            'verified',
        ]);

        $middleware->group('admin_only', [
            'auth',
            'active',
            'verified',
            'admin',
        ]);

        $middleware->group('super_admin_only', [
            'auth',
            'active',
            'verified',
            'super_admin',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
