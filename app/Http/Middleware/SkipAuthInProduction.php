<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware: SkipAuthInProduction
 * 
 * Purpose: Bypass login di production (Vercel), auto-login sebagai admin ID=1
 * Usage: Attach ke route admin yang perlu proteksi
 * 
 * Behavior:
 * - Di LOCAL (APP_ENV=local): Cek auth normal, redirect ke login jika belum login
 * - Di PRODUCTION (APP_ENV=production): Auto-login sebagai admin ID=1, skip form login
 * 
 * Security:
 * - ⚠️ SEMUA ORANG bisa akses admin di production tanpa password
 * - ✅ Cocok untuk: Internal tools, staging, demo
 * - ❌ JANGAN pakai untuk: Production dengan data sensitif/finansial
 */
class SkipAuthInProduction
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check environment
        $isProduction = app()->environment('production');
        
        if ($isProduction) {
            // PRODUCTION: Auto-login sebagai admin ID=1 tanpa cek password
            if (!Auth::check()) {
                $adminUser = User::find(1); // Admin user dengan ID=1
                
                if ($adminUser) {
                    // Force login tanpa password verification
                    Auth::login($adminUser);
                    
                    // Optional: Regenerate session untuk security
                    $request->session()->regenerate();
                }
            }
            
            // Lanjutkan ke request berikutnya (sudah authenticated)
            return $next($request);
        } else {
            // LOCAL/DEVELOPMENT: Pakai auth middleware standard
            // Redirect ke login jika belum authenticated
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
            }
            
            return $next($request);
        }
    }
}
