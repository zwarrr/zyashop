<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Users\ProductController;
use App\Http\Controllers\Admin\ProductAdminController;
use App\Http\Controllers\Admin\KategoriAdminController;
use App\Http\Controllers\Admin\CardAdminController;
use App\Http\Controllers\Admin\ProfileAdminController;

// User Routes - Public Pages
Route::get('/', [ProductController::class, 'home'])->name('home');
Route::get('/product', [ProductController::class, 'index'])->name('product');
Route::get('/cards-by-category/{category}', [ProductController::class, 'showCards'])->name('cards.show');
Route::get('/card/{cardId}/products', [ProductController::class, 'showProductsByCard'])->name('card.products');
Route::get('/products/{type}', [ProductController::class, 'showProductsByType'])->name('products.type');

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Debug route - remove after testing
Route::get('/debug-auth', function (Illuminate\Http\Request $request) {
    return response()->json([
        'auth_check' => Auth::check(),
        'auth_user' => Auth::user(),
        'session_id' => session()->getId(),
        'session_driver' => config('session.driver'),
        'cookies' => $request->cookies->all(),
        'has_zyashop_auth' => $request->hasCookie('zyashop_auth'),
    ]);
});

// Admin Routes - OPSI A: Auto-login di production, normal auth di local
Route::middleware('skip.auth.production')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    
    // Admin Product Management
    Route::get('/produk', [ProductAdminController::class, 'index'])->name('produk');
    Route::get('/produk/create', [ProductAdminController::class, 'create'])->name('produk.create');
    Route::get('/produk/{id}', [ProductAdminController::class, 'show'])->name('produk.show');
    Route::get('/produk/{id}/edit', [ProductAdminController::class, 'edit'])->name('produk.edit');
    
    // Legacy laporan route
    Route::get('/laporan', function () {
        return view('admin.laporan');
    })->name('laporan');
    
    // Kategori Admin Management
    Route::get('/kategori', [KategoriAdminController::class, 'index'])->name('kategori');
    Route::post('/kategori', [KategoriAdminController::class, 'store'])->name('kategori.store');
    Route::get('/kategori/{id}/edit', [KategoriAdminController::class, 'edit'])->name('kategori.edit');
    Route::put('/kategori/{id}', [KategoriAdminController::class, 'update'])->name('kategori.update');
    Route::delete('/kategori/{id}', [KategoriAdminController::class, 'destroy'])->name('kategori.destroy');
    
    // Cards Admin Management
    Route::get('/cards', [CardAdminController::class, 'index'])->name('cards');
    Route::post('/cards', [CardAdminController::class, 'store'])->middleware('force.json')->name('cards.store');
    Route::get('/cards/{id}/edit', [CardAdminController::class, 'edit'])->name('cards.edit');
    Route::get('/cards/{id}', [CardAdminController::class, 'show'])->name('cards.show');
    Route::put('/cards/{id}', [CardAdminController::class, 'update'])->middleware('force.json')->name('cards.update');
    Route::delete('/cards/{id}', [CardAdminController::class, 'destroy'])->middleware('force.json')->name('cards.destroy');
    
    // Admin Product Management (POST/PUT/DELETE with force.json)
    Route::post('/produk', [ProductAdminController::class, 'store'])->middleware('force.json')->name('produk.store');
    Route::put('/produk/{id}', [ProductAdminController::class, 'update'])->middleware('force.json')->name('produk.update');
    Route::delete('/produk/{id}', [ProductAdminController::class, 'destroy'])->middleware('force.json')->name('produk.destroy');
    
    // Profile Admin Management
    Route::get('/profile', [ProfileAdminController::class, 'index'])->name('profile');
    Route::post('/profile/update', [ProfileAdminController::class, 'update'])->name('profile.update');
    Route::post('/profile/links', [ProfileAdminController::class, 'storeLink'])->name('profile.links.store');
    Route::put('/profile/links/{id}', [ProfileAdminController::class, 'updateLink'])->name('profile.links.update');
    Route::delete('/profile/links/{id}', [ProfileAdminController::class, 'destroyLink'])->name('profile.links.destroy');
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

