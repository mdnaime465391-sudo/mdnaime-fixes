<?php

use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\Middleware\AuthenticateSession;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Plesk fronts Apache with an nginx proxy — trust it so HTTPS and
        // client IPs are detected correctly.
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'admin' => EnsureAdmin::class,
        ]);

        // Needed for Auth::logoutOtherDevices() (Change password) to really
        // sign out the admin's other sessions.
        $middleware->web(append: [AuthenticateSession::class, SecurityHeaders::class]);
        $middleware->api(append: [SecurityHeaders::class]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
