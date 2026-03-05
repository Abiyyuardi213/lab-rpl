<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role.praktikan' => \App\Http\Middleware\EnsurePraktikanRole::class,
            'role.aslab' => \App\Http\Middleware\EnsureAslabRole::class,
            'role.admin' => \App\Http\Middleware\EnsureAdminRole::class,
            'role.superadmin' => \App\Http\Middleware\EnsureSuperAdminRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
