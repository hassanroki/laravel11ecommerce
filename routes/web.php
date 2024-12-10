<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WishListController;
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

// Apply Coupon Code
Route::post('/cart/apply-coupon', [CartController::class, 'applyCouponCode'])->name('cart.coupon.apply');
Route::delete('/cart/remove-coupon', [CartController::class, 'removeCouponCode'])->name('cart.coupon.remove');

// Add To Wish List
Route::post('/wishlist/add', [WishListController::class, 'addToWishList'])->name('wishlist.add');
Route::get('/wishlist', [WishListController::class, 'index'])->name('wishlist.index');
Route::delete('/wishlist/item/remove/{rowId}', [WishListController::class, 'removeItem'])->name('wishlist.item.remove');
Route::delete('/wishlist/clear', [WishListController::class, 'emptyWishlist'])->name('wishlist.item.clear');
Route::post('/wishlist/move-to-cart/{rowId}', [WishListController::class, 'moveToCart'])->name('wishlist.move.to.cart');

// Checkout
Route::get('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
Route::post('/place-an-order', [CartController::class, 'placeAnOrder'])->name('cart.place.an.order');
Route::get('order-confirmation', [CartController::class, 'orderConfirmation'])->name('cart.order.confrimation');

Route::get('/contact-us', [HomeController::class, 'contact'])->name('home.contact');
Route::post('/contact-us/store', [HomeController::class, 'contactStore'])->name('home.contact.store');

// Search Product
Route::get('/search', [HomeController::class, 'searchProduct'])->name('home.search');

Route::middleware(['auth'])->group(function () {
    Route::get('/account-dashboard', [UserController::class, 'index'])->name('user.index');

    // Order
    Route::get('/account-orders', [UserController::class, 'orders'])->name('user.orders');
    Route::get('/account-orders/{order_id}/details', [UserController::class, 'orderDetails'])->name('user.order.details');
    Route::put('/account-orders/cancel-order', [UserController::class, 'orderCancel'])->name('user.order.cancel');
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

    // Coupons
    Route::get('/admin/coupons', [AdminController::class, 'coupons'])->name('admin.coupons');
    Route::get('/admin/coupon/add', [AdminController::class, 'couponAdd'])->name('admin.coupon.add');
    Route::post('/admin/coupon/store', [AdminController::class, 'couponStore'])->name('admin.coupon.store');
    Route::get('/admin/coupon/{id}/edit', [AdminController::class, 'couponEdit'])->name('admin.coupon.edit');
    Route::post('/admin/coupon/{id}/update', [AdminController::class, 'couponUpdate'])->name('admin.coupon.update');
    Route::delete('/admin/coupon/{id}/delete', [AdminController::class, 'couponDelete'])->name('admin.coupon.delete');

    // Order
    Route::get('/admin/orders', [AdminController::class, 'orders'])->name('admin.orders');
    Route::get('/admin/order/{order_id}/details', [AdminController::class, 'orderDetails'])->name('admin.order.details');
    Route::put('/admin/order/update-status', [AdminController::class, 'updateOrderStatus'])->name('admin.order.status.update');

    // Slide
    Route::get('/admin/slides', [AdminController::class, 'slide'])->name('admin.slide');
    Route::get('/admin/slides/add', [AdminController::class, 'slideAdd'])->name('admin.slide.add');
    Route::post('/admin/slides/add', [AdminController::class, 'slideStore'])->name('admin.slide.store');
    Route::get('/admin/slides/{id}/edit', [AdminController::class, 'slideEdit'])->name('admin.slide.edit');
    Route::post('/admin/slides/{id}/edit', [AdminController::class, 'slideUpdate'])->name('admin.slide.update');
    Route::delete('/admin/slides/{id}/delete', [AdminController::class, 'slideDelete'])->name('admin.slide.delete');

    // Contacts
    Route::get('/admin/contact', [AdminController::class, 'contacts'])->name('admin.contact');
    Route::delete('/admin/contact/{id}/delete', [AdminController::class, 'contactDelete'])->name('admin.contact.delete');

    // Admin Search Product
    Route::get('/admin/search', [AdminController::class, 'searchProduct'])->name('admin.search');
});
