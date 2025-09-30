<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'active' => \App\Http\Middleware\ActiveMiddleware::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'super_admin' => \App\Http\Middleware\SuperAdminMiddleware::class,
            'cashier' => \App\Http\Middleware\CashierMiddleware::class,
            'admin_cashier' => \App\Http\Middleware\AdminOrCashierMiddleware::class,
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

        $middleware->group('cashier_only', [
            'auth',
            'active',
            'verified',
            'cashier',
        ]);

        $middleware->group('admin_or_cashier', [
            'auth',
            'active',
            'verified',
            'admin_cashier',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
