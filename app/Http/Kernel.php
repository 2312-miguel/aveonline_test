<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middlewareAliases = [
        'check.token' => \App\Http\Middleware\CheckSecurityToken::class,
    ];

    protected $routeMiddleware = [
        'auth.token' => \App\Http\Middleware\CheckSecurityToken::class,
    ];
}
