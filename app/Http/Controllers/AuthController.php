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
            $request->session()->regenerate();
            
            // Set simple cookie with user ID (encrypted by Laravel automatically)
            $userId = Auth::id();
            
            return redirect()->intended('/dashboard')
                ->cookie('admin_token', $userId, 10080); // 7 days
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
        return redirect('/login')->cookie('admin_token', '', -1);
    }

    /**
     * Show dashboard (protected route)
     */
    public function dashboard(Request $request)
    {
        // Check cookie first
        $userId = $request->cookie('admin_token');
        
        if (!$userId) {
            return redirect('/login');
        }
        
        // Find and login user from cookie
        $user = User::find($userId);
        if (!$user) {
            return redirect('/login')->cookie('admin_token', '', -1);
        }
        
        // Auto login from cookie
        Auth::login($user);
        
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
