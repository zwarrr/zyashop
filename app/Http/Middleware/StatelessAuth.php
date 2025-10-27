<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Models\User;

class StatelessAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Check if auth cookie exists
        $authCookie = $request->cookie('vercel_auth');
        
        if ($authCookie) {
            try {
                // Decrypt and get user ID
                $userId = Crypt::decryptString($authCookie);
                
                // Find user and login
                $user = User::find($userId);
                if ($user) {
                    Auth::login($user);
                }
            } catch (\Exception $e) {
                // Invalid cookie, ignore
            }
        }
        
        return $next($request);
    }
}
