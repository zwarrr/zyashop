<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Users\ProductController;
use App\Http\Controllers\Admin\ProductAdminController;
use App\Http\Controllers\Admin\KategoriAdminController;
use App\Http\Controllers\Admin\CardAdminController;
use App\Http\Controllers\Admin\ProfileAdminController;

// ============================================
// PUBLIC ROUTES
// ============================================

// User Routes - Public Pages
Route::get('/', [ProductController::class, 'home'])->name('home');
Route::get('/product', [ProductController::class, 'index'])->name('product');
Route::get('/cards/{category}', [ProductController::class, 'showCards'])->name('cards.show');
Route::get('/card/{cardId}/products', [ProductController::class, 'showProductsByCard'])->name('card.products');
Route::get('/products/{type}', [ProductController::class, 'showProductsByType'])->name('products.type');

// ============================================
// AUTH ROUTES (Optional - bisa dihapus jika pakai auto-login)
// ============================================

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ============================================
// ADMIN ROUTES - PILIH SALAH SATU OPSI!
// ============================================

// -------------------------------
// OPSI A: Auto-login di Production
// -------------------------------
// Di production (Vercel): Auto-login sebagai admin ID=1
// Di local: Pakai login form normal
// 
// Usage: Langsung akses https://zyashop.vercel.app/dashboard
//
// Route::middleware('skip.auth.production')->group(function () {
//     Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
//     
//     Route::get('/produk', [ProductAdminController::class, 'index'])->name('produk');
//     Route::get('/produk/create', [ProductAdminController::class, 'create'])->name('produk.create');
//     Route::post('/produk', [ProductAdminController::class, 'store'])->name('produk.store');
//     Route::get('/produk/{id}/edit', [ProductAdminController::class, 'edit'])->name('produk.edit');
//     Route::put('/produk/{id}', [ProductAdminController::class, 'update'])->name('produk.update');
//     Route::delete('/produk/{id}', [ProductAdminController::class, 'destroy'])->name('produk.destroy');
//     
//     Route::get('/kategori', [KategoriAdminController::class, 'index'])->name('kategori');
//     Route::post('/kategori', [KategoriAdminController::class, 'store'])->name('kategori.store');
//     Route::get('/kategori/{id}/edit', [KategoriAdminController::class, 'edit'])->name('kategori.edit');
//     Route::put('/kategori/{id}', [KategoriAdminController::class, 'update'])->name('kategori.update');
//     Route::delete('/kategori/{id}', [KategoriAdminController::class, 'destroy'])->name('kategori.destroy');
//     
//     Route::get('/cards', [CardAdminController::class, 'index'])->name('cards');
//     Route::post('/cards', [CardAdminController::class, 'store'])->name('cards.store');
//     Route::get('/cards/{id}/edit', [CardAdminController::class, 'edit'])->name('cards.edit');
//     Route::put('/cards/{id}', [CardAdminController::class, 'update'])->name('cards.update');
//     Route::delete('/cards/{id}', [CardAdminController::class, 'destroy'])->name('cards.destroy');
//     
//     Route::get('/profile', [ProfileAdminController::class, 'index'])->name('profile');
//     Route::put('/profile', [ProfileAdminController::class, 'update'])->name('profile.update');
//     
//     Route::get('/laporan', function () {
//         return view('admin.laporan');
//     })->name('laporan');
// });

// -------------------------------
// OPSI B: Admin Access Key (RECOMMENDED)
// -------------------------------
// Butuh token di URL: ?key=ZyaShop2025SecretKey!
// Lebih aman karena butuh token rahasia
//
// Usage: https://zyashop.vercel.app/dashboard?key=ZyaShop2025SecretKey!
//
Route::middleware('admin.access.key')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    
    // Admin Product Management
    Route::get('/produk', [ProductAdminController::class, 'index'])->name('produk');
    Route::get('/produk/create', [ProductAdminController::class, 'create'])->name('produk.create');
    Route::post('/produk', [ProductAdminController::class, 'store'])->name('produk.store');
    Route::get('/produk/{id}/edit', [ProductAdminController::class, 'edit'])->name('produk.edit');
    Route::put('/produk/{id}', [ProductAdminController::class, 'update'])->name('produk.update');
    Route::delete('/produk/{id}', [ProductAdminController::class, 'destroy'])->name('produk.destroy');
    
    // Kategori Admin Management
    Route::get('/kategori', [KategoriAdminController::class, 'index'])->name('kategori');
    Route::post('/kategori', [KategoriAdminController::class, 'store'])->name('kategori.store');
    Route::get('/kategori/{id}/edit', [KategoriAdminController::class, 'edit'])->name('kategori.edit');
    Route::put('/kategori/{id}', [KategoriAdminController::class, 'update'])->name('kategori.update');
    Route::delete('/kategori/{id}', [KategoriAdminController::class, 'destroy'])->name('kategori.destroy');
    
    // Cards Admin Management
    Route::get('/cards', [CardAdminController::class, 'index'])->name('cards');
    Route::post('/cards', [CardAdminController::class, 'store'])->name('cards.store');
    Route::get('/cards/{id}/edit', [CardAdminController::class, 'edit'])->name('cards.edit');
    Route::put('/cards/{id}', [CardAdminController::class, 'update'])->name('cards.update');
    Route::delete('/cards/{id}', [CardAdminController::class, 'destroy'])->name('cards.destroy');
    
    // Profile Admin Management
    Route::get('/profile', [ProfileAdminController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileAdminController::class, 'update'])->name('profile.update');
    
    // Legacy laporan route
    Route::get('/laporan', function () {
        return view('admin.laporan');
    })->name('laporan');
});

// ============================================
// DEBUG ROUTES (Remove in production)
// ============================================

Route::get('/debug-auth', function (Illuminate\Http\Request $request) {
    return response()->json([
        'environment' => app()->environment(),
        'auth_check' => Auth::check(),
        'auth_user' => Auth::user(),
        'session_id' => session()->getId(),
        'session_driver' => config('session.driver'),
        'admin_access_key' => env('ADMIN_ACCESS_KEY') ? '***SET***' : 'NOT SET',
    ]);
});
