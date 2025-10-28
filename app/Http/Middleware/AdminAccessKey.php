<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware: AdminAccessKey
 * 
 * Purpose: Proteksi admin dengan access key (token) di URL atau header
 * Usage: Tambahkan `?key=RAHASIA123` di URL untuk akses admin tanpa login
 * 
 * Behavior:
 * - Check query parameter `key` atau header `X-Admin-Key`
 * - Jika match dengan ADMIN_ACCESS_KEY di .env → auto-login sebagai admin
 * - Jika tidak match → redirect ke login (di local) atau 403 (di production)
 * 
 * Example Usage:
 * - https://zyashop.vercel.app/dashboard?key=RAHASIA123
 * - Header: X-Admin-Key: RAHASIA123
 * 
 * Security:
 * - ✅ Lebih aman dari auto-login (butuh token)
 * - ✅ Token bisa di-rotasi (ganti ADMIN_ACCESS_KEY)
 * - ⚠️ Jangan share token di public
 * - ✅ Cocok untuk: Staging, testing, internal access
 */
class AdminAccessKey
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get access key from query parameter or header
        $providedKey = $request->input('key') ?? $request->header('X-Admin-Key');
        
        // Get correct access key from environment
        $correctKey = env('ADMIN_ACCESS_KEY');
        
        // Option 1: Access key provided and correct
        if ($providedKey && $correctKey && $providedKey === $correctKey) {
            // Auto-login sebagai admin ID=1
            if (!Auth::check()) {
                $adminUser = User::find(1);
                
                if ($adminUser) {
                    Auth::login($adminUser);
                    $request->session()->regenerate();
                }
            }
            
            return $next($request);
        }
        
        // Option 2: Already authenticated (via normal login)
        if (Auth::check()) {
            return $next($request);
        }
        
        // Option 3: Not authenticated and no valid key
        $isProduction = app()->environment('production');
        
        if ($isProduction) {
            // Production: Return 403 Forbidden
            abort(403, 'Access denied. Valid access key required.');
        } else {
            // Local: Redirect to login
            return redirect()->route('login')->with('error', 'Silakan login atau gunakan access key.');
        }
    }
}
