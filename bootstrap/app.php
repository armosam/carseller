<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsureTodayMonday;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Disable CSRF validation for mentioned paths
        $middleware->validateCsrfTokens(except: [
            'stripe/*'
        ]);

        // Using the custom middleware globally
        //$middleware->append(EnsureTodayMonday::class);

        // Define alias of middleware
        $middleware->alias([
            'EnsureTodayMonday' => EnsureTodayMonday::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
