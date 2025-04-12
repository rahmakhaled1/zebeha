<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\OfferController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SliderController;
use App\Http\Controllers\Api\SupCategoryController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/users', [UserController::class, 'index']);
Route::prefix('user')->group(function () {
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/verify-otp', [UserController::class, 'verifyOtp']);
    Route::post('/forgot-password', [UserController::class, 'forgotPassword']);
    Route::post('/reset-password', [UserController::class, 'resetPassword']);
    Route::post('/logout', [UserController::class, 'logout']);
});

Route::prefix('address')->group(function () {
    Route::post('/create', [AddressController::class, 'store']);
});

Route::prefix('slider')->group(function () {
    Route::post('/create', [SliderController::class, 'create']);
    Route::get('/fetch-sliders', [SliderController::class, 'fetch_sliders']);
});

Route::prefix('category')->group(function () {
    Route::post('/create', [CategoryController::class, 'create']);
    Route::get('/fetch-categories', [CategoryController::class, 'fetch_categories']);
    Route::get('/fetch-category/{id}', [CategoryController::class, 'fetch_category']);
});

Route::prefix('supcategory')->group(function () {
    Route::post('/create', [SupCategoryController::class, 'create']);
    Route::get('/fetch-supcategories', [SupCategoryController::class, 'fetch_supcategories']);
    Route::get('/fetch-supcategory/{id}', [SupCategoryController::class, 'fetch_supcategory']);
});

Route::prefix('product')->group(function () {
    Route::post('/create', [ProductController::class, 'create']);
    Route::get('/fetch-product/{id}', [ProductController::class, 'fetch_product']);
    Route::get('/fetch-products', [ProductController::class, 'fetch_products']);
    Route::get('/fetch-top-selling-products', [ProductController::class, 'getTopSellingProducts']);
});

Route::prefix('offer')->group(function () {
    Route::post('/create', [OfferController::class, 'create']);
    Route::get('/fetch-offers', [OfferController::class, 'fetch_offers']);
    Route::get('/fetch-offer/{id}', [OfferController::class, 'fetch_offer']);
});

Route::prefix('coupon')->group(function () {
    Route::post('/create', [CouponController::class, 'create']);
});

Route::prefix('cart')->group(function () {
    Route::post('/add', [CartController::class, 'addToCart']);
    Route::get('/', [CartController::class, 'viewCart']);
    Route::delete('/item/{id}', [CartController::class, 'removeFromCart']);
    Route::delete('/clear', [CartController::class, 'clearCart']);
});

Route::prefix('order')->group(function () {
    Route::post('/place', [OrderController::class, 'placeOrder']);
    Route::get('/', [OrderController::class, 'listOrders']);
});


