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

        if (Auth::attempt($credentials, true)) { // Remember me = true
            $request->session()->regenerate();
            
            // Force save session
            $request->session()->put('auth', [
                'user_id' => Auth::id(),
                'logged_in' => true,
                'time' => time()
            ]);
            $request->session()->save();
            
            // Set custom encrypted cookie for stateless auth in Vercel
            $encryptedUserId = \Illuminate\Support\Facades\Crypt::encryptString(Auth::id());
            
            return redirect('/dashboard')
                ->with('success', 'Login berhasil!')
                ->cookie('vercel_auth', $encryptedUserId, 10080, '/', null, true, true); // 7 days, HttpOnly, Secure
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
        
        // Delete custom auth cookie
        return redirect('/login')
            ->with('success', 'Logout berhasil!')
            ->cookie('vercel_auth', '', -1); // Delete cookie
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
