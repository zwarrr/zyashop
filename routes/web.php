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
Route::get('/cards/{category}', [ProductController::class, 'showCards'])->name('cards.show');
Route::get('/card/{cardId}/products', [ProductController::class, 'showProductsByCard'])->name('card.products');
Route::get('/products/{type}', [ProductController::class, 'showProductsByType'])->name('products.type');

// Image Serving Route - Compatible with Vercel (no symlink needed)
Route::get('/storage/{path}', function ($path) {
    // Use /tmp in production (Vercel), storage in local
    $basePath = app()->environment('production') ? '/tmp/storage' : storage_path('app/public');
    $storagePath = $basePath . '/' . $path;
    
    if (!file_exists($storagePath)) {
        abort(404, 'Image not found');
    }
    
    // Get file extension and determine MIME type
    $extension = strtolower(pathinfo($storagePath, PATHINFO_EXTENSION));
    $mimeTypes = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'svg' => 'image/svg+xml',
    ];
    
    $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';
    
    \Log::info('Serving image', [
        'path' => $path,
        'extension' => $extension,
        'mime' => $mimeType,
        'size' => filesize($storagePath)
    ]);
    
    return response()->file($storagePath, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('path', '.*')->name('storage.serve');

// Test Storage Route - Check if storage is writable on Vercel
Route::get('/test-storage', function () {
    $isProduction = app()->environment('production');
    $basePath = $isProduction ? '/tmp/storage' : storage_path('app/public');
    
    $results = [
        'environment' => app()->environment(),
        'base_path' => $basePath,
        'is_writable' => is_writable(dirname($basePath)),
        'base_exists' => file_exists($basePath),
        'cards_dir' => $basePath . '/cards',
        'cards_dir_exists' => file_exists($basePath . '/cards'),
        'tmp_writable' => is_writable('/tmp'),
    ];
    
    // List files in cards directory
    if (file_exists($basePath . '/cards')) {
        $results['cards_files'] = scandir($basePath . '/cards');
    } else {
        $results['cards_files'] = [];
    }
    
    // Try create test file
    try {
        if (!file_exists($basePath)) {
            mkdir($basePath, 0755, true);
        }
        $testFile = $basePath . '/test.txt';
        file_put_contents($testFile, 'test');
        $results['test_write'] = 'SUCCESS - File created at: ' . $testFile;
        @unlink($testFile);
    } catch (\Exception $e) {
        $results['test_write'] = 'FAILED: ' . $e->getMessage();
    }
    
    return response()->json($results);
});

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

