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
        $middleware->trustProxies(at: '*', headers: 
            \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO
        );
        
        // Disable CSRF for all routes
        $middleware->validateCsrfTokens(except: [
            '*',
        ]);
        
        // Register custom middleware aliases
        $middleware->alias([
            'skip.auth.production' => \App\Http\Middleware\SkipAuthInProduction::class,
            'admin.access.key' => \App\Http\Middleware\AdminAccessKey::class,
            'force.json' => \App\Http\Middleware\ForceJsonResponse::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Force JSON response for AJAX requests
        $exceptions->respond(function ($response, $exception, $request) {
            if ($request->expectsJson() || $request->ajax() || $request->is('cards') || $request->is('produk')) {
                if ($exception instanceof \Illuminate\Validation\ValidationException) {
                    return response()->json([
                        'error' => 'Validasi gagal',
                        'errors' => $exception->errors()
                    ], 422);
                }
                
                return response()->json([
                    'error' => $exception->getMessage() ?: 'Terjadi kesalahan server'
                ], 500);
            }
            
            return $response;
        });
    })->create();
