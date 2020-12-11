<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserRegisterController;
use App\Http\Controllers\UserLoginController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\ShoppingCartController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});



//Route::get('/user', [UserRegisterController::class,'index']);
//Route::post('/register_buyer', [UserRegisterController::class,'register_buyer']);
//Route::post('/register_seller', [UserRegisterController::class,'register_seller']);
//Route::post('/login_buyer', [UserLoginController::class,'login_buyer']);
//Route::post('/login_seller', [UserLoginController::class,'login_seller']);
//Route::post('/upload_product', [UploadProductController::class,'add_product']);

Route::get('/api/auth/login',[UserLoginController::class,'do_login']);
Route::group([
    'prefix' => 'auth'
], function () {
        Route::post('/register_buyer', [UserRegisterController::class,'register_buyer']);
    Route::post('/register_seller', [UserRegisterController::class,'register_seller']);
    Route::post('/login_buyer', [UserLoginController::class,'login_buyer']);
    Route::post('/login_seller', [UserLoginController::class,'login_seller']);
    Route::get('/api/auth/login',[UserLoginController::class,'do_login']);
    //Route::get('login',  [UserLoginController::class,'do']);

    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        //Route::get('logout', 'AuthController@logout');
        //Route::get('user', [ProductController::class,'user']);
        Route::get('/api/auth/login',[UserLoginController::class,'do_login']);
        
        // route get basic info
        // route get province
        Route::get ('get_province',[UserLoginController::class,'get_province']);
        Route::get ('get_city/{id}',[UserLoginController::class,'get_city']);
        
        // route get user info
        Route::get ('get_user',[UserLoginController::class,'get_user']);
        
        //route edit user account
        Route::put ('edit_location',[UserLoginController::class,'edit_location']);
        Route::put ('edit_bank',[UserLoginController::class,'edit_bank']);
        Route::put ('edit_phone',[UserLoginController::class,'edit_phone']);
        
        // route product
        Route::post('add_product', [ProductController::class,'add_product']);
        Route::post('add_product_qty', [ProductController::class,'add_product_qty']);
        Route::put('edit_product_qty/{id}', [ProductController::class,'edit_product_qty']);
        Route::put('edit_product/{id}', [ProductController::class,'edit_product']);
        Route::put('delete_product/{id}', [ProductController::class,'delete_product']);
        Route::put('delete_product_qty/{id}', [ProductController::class,'delete_product_qty']);
        Route::get('get_product/{id}', [ProductController::class,'get_product']);
        Route::post('image_product/{id}', [ProductController::class,'image_product']);
        Route::get('get_products', [ProductController::class,'get_products']);
        //route wishlist
        Route::get('get_wishlist/{id}', [WishlistController::class,'get_wishlist']);
        Route::post('add_wishlist', [WishlistController::class,'add_wishlist']);
        Route::put('edit_wishlist/{id}', [WishlistController::class,'edit_wishlist']);
        Route::put('delete_wishlist/{id}', [WishlistController::class,'delete_wishlist']);

        //shoping chart
        Route::post('add_shopping_chart', [ShoppingCartController::class,'add_shopping_chart']);
        Route::get('get_shopping_chart/{id}', [ShoppingCartController::class,'get_shopping_chart']);
        Route::put('edit_shopping_chart/{id}', [ShoppingCartController::class,'edit_shopping_chart']);
        Route::put('delete_shopping_chart/{id}', [ShoppingCartController::class,'delete_shopping_chart']);
        Route::put('delete_per_seller_shopping_chart/{id}', [ShoppingCartController::class,'delete_per_seller_shopping_chart']);
        Route::post('check_delivery_cost', [ShoppingCartController::class,'check_delivery_cost']);

        //checkout
        Route::put('checkout_shopping_chart/{id}', [ShoppingCartController::class,'checkout_shopping_chart']);
        Route::put('checkout_per_seller_shopping_chart/{id}', [ShoppingCartController::class,'checkout_per_seller_shopping_chart']);

    });
});
