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
        $middleware->trustProxies(at: '*');
        $middleware->trustHosts(at: [
            fn () => '.*\.vercel\.app',  // Regex pattern untuk semua subdomain vercel
            'localhost',
            '127.0.0.1'
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
