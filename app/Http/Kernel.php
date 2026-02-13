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
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Http\Middleware\SetCacheHeaders::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ];

    /**
     * The application's middleware aliases.
     *
     * @var array
     */
    protected $middlewareAliases = [
        // ... other middleware aliases ...
        'check_module_access' => \App\Http\Middleware\CheckBusinessModuleAccess::class,
        'check.module' => \App\Http\Middleware\CheckModuleAndPermission::class,
    ];
}