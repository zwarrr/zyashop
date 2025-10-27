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

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes - NO AUTH MIDDLEWARE, check in controller
Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

// Admin Product Management
Route::get('/produk', [ProductAdminController::class, 'index'])->name('produk');
Route::get('/produk/create', [ProductAdminController::class, 'create'])->name('produk.create');
Route::post('/produk', [ProductAdminController::class, 'store'])->name('produk.store');
Route::get('/produk/{id}/edit', [ProductAdminController::class, 'edit'])->name('produk.edit');
Route::put('/produk/{id}', [ProductAdminController::class, 'update'])->name('produk.update');
Route::delete('/produk/{id}', [ProductAdminController::class, 'destroy'])->name('produk.destroy');

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
Route::post('/cards', [CardAdminController::class, 'store'])->name('cards.store');
Route::get('/cards/{id}/edit', [CardAdminController::class, 'edit'])->name('cards.edit');
Route::put('/cards/{id}', [CardAdminController::class, 'update'])->name('cards.update');
Route::delete('/cards/{id}', [CardAdminController::class, 'destroy'])->name('cards.destroy');

// Profile Admin Management
Route::get('/profile', [ProfileAdminController::class, 'index'])->name('profile');
Route::post('/profile/update', [ProfileAdminController::class, 'update'])->name('profile.update');
Route::post('/profile/links', [ProfileAdminController::class, 'storeLink'])->name('profile.links.store');
Route::put('/profile/links/{id}', [ProfileAdminController::class, 'updateLink'])->name('profile.links.update');
Route::delete('/profile/links/{id}', [ProfileAdminController::class, 'destroyLink'])->name('profile.links.destroy');

