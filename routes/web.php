<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Users\ProductController;
use App\Http\Controllers\Admin\ProductAdminController;
use App\Http\Controllers\Admin\KategoriAdminController;
use App\Http\Controllers\Admin\CardAdminController;
use App\Http\Controllers\Admin\ProfileAdminController;

// Image Serving Route - MUST BE FIRST! Support both with/without subfolder
Route::get('/storage/{path}', function ($path) {
    $basePath = app()->environment('production') ? '/tmp/storage' : storage_path('app/public');
    
    // Try direct path first (new format)
    $filePath = $basePath . '/' . $path;
    
    // If not found and path has subfolder, try without subfolder (fallback)
    if (!file_exists($filePath) && strpos($path, '/') !== false) {
        $filename = basename($path);
        $filePath = $basePath . '/' . $filename;
    }
    
    if (!file_exists($filePath)) {
        return response()->json(['error' => 'File not found: ' . $path], 404);
    }
    
    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    $mimeTypes = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
    ];
    
    $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';
    
    return response()->file($filePath, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('path', '.*');

// User Routes - Public Pages
Route::get('/', [ProductController::class, 'home'])->name('home');
Route::get('/product', [ProductController::class, 'index'])->name('product');
Route::get('/cards/{category}', [ProductController::class, 'showCards'])->name('cards.show');
Route::get('/card/{cardId}/products', [ProductController::class, 'showProductsByCard'])->name('card.products');
Route::get('/products/{type}', [ProductController::class, 'showProductsByType'])->name('products.type');

// Clear cache route (for debugging)
Route::get('/clear-cache', function () {
    \Artisan::call('cache:clear');
    \Artisan::call('config:clear');
    \Artisan::call('view:clear');
    
    // Delete route cache file manually
    $routeCachePath = '/tmp/routes.php';
    if (file_exists($routeCachePath)) {
        unlink($routeCachePath);
    }
    
    return response()->json([
        'success' => true,
        'message' => 'All caches cleared!',
        'cleared' => [
            'cache' => true,
            'config' => true,
            'views' => true,
            'routes' => file_exists($routeCachePath) ? false : true
        ]
    ]);
});

// Fix database image paths (run once)
Route::get('/fix-image-paths', function () {
    $updated = \DB::table('cards')
        ->where('image', 'like', 'cards/%')
        ->update(['image' => \DB::raw("REPLACE(image, 'cards/', '')")]);
    
    return response()->json([
        'success' => true,
        'message' => 'Fixed image paths',
        'updated_rows' => $updated
    ]);
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

