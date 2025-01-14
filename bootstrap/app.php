<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Create and configure the application instance
return Application::configure(basePath: dirname(__DIR__))
    // Set up routing paths
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    // Register middleware aliases
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias(['check.token' => \App\Http\Middleware\CheckSecurityToken::class]);
    })

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias(['log.activity' => \App\Http\Middleware\LogActivity::class]);
    })
    // Register exception handlers
    ->withExceptions(function (Exceptions $exceptions) {
        // Add exception handling logic here if needed
    })
    // Create the application instance
    ->create();
