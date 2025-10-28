<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class VercelAuthMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // First check standard Laravel session auth
        if (Auth::check()) {
            return $next($request);
        }
        
        // Fallback: Check stateless cookie auth for Vercel
        $authCookie = $request->cookie('zyashop_auth');
        
        if ($authCookie) {
            try {
                $authData = decrypt($authCookie);
                
                // Check if cookie expired
                if (isset($authData['expires']) && $authData['expires'] > time()) {
                    // Find user and login
                    $user = User::find($authData['user_id']);
                    
                    if ($user && $user->email === $authData['email']) {
                        Auth::login($user);
                        return $next($request);
                    }
                }
            } catch (\Exception $e) {
                // Cookie invalid or corrupted, ignore
            }
        }
        
        // Not authenticated
        return redirect()->route('login');
    }
}
