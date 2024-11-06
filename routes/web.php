<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AuthAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{product_slug}', [ShopController::class, 'view'])->name('product.shop.view');

// Cart
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'addCart'])->name('cart.add');
Route::put('/cart/increaseQty/{rowId}', [CartController::class, 'increaseCartQty'])->name('cartQty.increase');
Route::put('/cart/decreaseQty/{rowId}', [CartController::class, 'decreaseCartQty'])->name('cartQty.decrease');
Route::delete('/cart/remove/{rowId}', [CartController::class, 'removeItem'])->name('cart.item.remove');
Route::delete('/cart/clear', [CartController::class, 'emptyCart'])->name('cart.empty');



Route::get('/test', function () {
    return view('test');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/account-dashboard', [UserController::class, 'index'])->name('user.index');
});

Route::middleware(['auth', AuthAdmin::class])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');

    // Brand
    Route::get('/admin/brand', [AdminController::class, 'brand'])->name('admin.brand');
    Route::get('/admin/brand/create', [AdminController::class, 'brandCreate'])->name('admin.brand.create');
    Route::post('/admin/brand/store', [AdminController::class, 'brandStore'])->name('admin.brand.store');
    Route::get('/admin/brand/edit/{id}', [AdminController::class, 'brandEdit'])->name('admin.brand.edit');
    Route::post('/admin/brand/update/{id}', [AdminController::class, 'brandUpdate'])->name('admin.brand.update');
    Route::delete('/admin/brand/delete/{id}', [AdminController::class, 'brandDelete'])->name('admin.brand.destory');

    // Categories
    Route::get('/admin/categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::get('/admin/category/create', [AdminController::class, 'categoryCreate'])->name('admin.category.create');
    Route::post('/admin/category/store', [AdminController::class, 'categoryStore'])->name('admin.category.store');
    Route::get('/admin/category/edit/{id}', [AdminController::class, 'categoryEdit'])->name('admin.category.edit');
    Route::post('/admin/category/update/{id}', [AdminController::class, 'categoryUpdate'])->name('admin.category.update');
    Route::delete('/admin/category/delete/{id}', [AdminController::class, 'categoryDelete'])->name('admin.category.delete');

    // Products
    Route::resource('/admin/products', ProductController::class);
});
