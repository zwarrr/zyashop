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
        
        // Stateless auth - check cookie BEFORE auth middleware
        $middleware->web(prepend: [
            function ($request, $next) {
                // Check if auth cookie exists
                $authCookie = $request->cookie('vercel_auth');
                
                if ($authCookie) {
                    try {
                        // Decrypt and get user ID
                        $userId = \Illuminate\Support\Facades\Crypt::decryptString($authCookie);
                        
                        // Find user and login
                        $user = \App\Models\User::find($userId);
                        if ($user) {
                            \Illuminate\Support\Facades\Auth::login($user);
                        }
                    } catch (\Exception $e) {
                        // Invalid cookie, ignore
                    }
                }
                
                return $next($request);
            }
        ]);
        
        // Disable CSRF for all POST/PUT/DELETE routes
        $middleware->validateCsrfTokens(except: [
            '*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
