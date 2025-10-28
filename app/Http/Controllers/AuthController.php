<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if (Auth::attempt($credentials, true)) {
            $user = Auth::user();
            
            // Create a stateless auth cookie
            $authData = encrypt([
                'user_id' => $user->id,
                'email' => $user->email,
                'expires' => now()->addDays(7)->timestamp
            ]);
            
            $request->session()->regenerate();
            
            // Return redirect with cookie attached
            return redirect()->intended('/dashboard')->cookie(
                'zyashop_auth', 
                $authData, 
                10080, // 7 days in minutes
                '/',
                null,
                false, // secure (set true for production HTTPS)
                true, // httpOnly
                false, // raw
                'lax' // sameSite
            );
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Clear stateless auth cookie and redirect
        return redirect('/login')->withCookie(cookie()->forget('zyashop_auth'));
    }

    /**
     * Show dashboard (protected route)
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        // Get statistics
        $totalProducts = $user->products()->count();
        $totalCards = $user->cards()->count();
        $totalCategories = $user->categories()->count();
        $activeProducts = $user->products()->where('status', 'active')->count();
        $inactiveProducts = $user->products()->where('status', 'inactive')->count();
        $comingSoonProducts = $user->products()->where('status', 'coming_soon')->count();
        
        return view('admin.dashboard', compact(
            'totalProducts',
            'totalCards', 
            'totalCategories',
            'activeProducts',
            'inactiveProducts',
            'comingSoonProducts'
        ));
    }
}
