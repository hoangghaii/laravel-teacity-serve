<?php

use App\Http\Controllers\Account\AccountController;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\Order\OrderController;
use App\Http\Controllers\Product\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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

Route::post('signup', [AccountController::class, 'register']);
Route::post('login', [AccountController::class, 'login']);

Route::group(['middleware' => 'jwt.auth'], function () {
    Route::get('auth', [AccountController::class, 'user']);
    Route::post('logout', [AccountController::class, 'logout']);
});

// Route::middleware('jwt.refresh')->get('/token/refresh', 'Account\AccountController@refresh');
Route::post('/category', [CategoryController::class, 'store']);
Route::get('/category', [CategoryController::class, 'index']);
Route::put('/category/{id}', [CategoryController::class, 'update']);
Route::delete('/category/{id}', [CategoryController::class, 'destroy']);


Route::post('/product', [ProductController::class, 'store']);
Route::get('/product', [ProductController::class, 'index']);
Route::post('/product/{id}', [ProductController::class, 'update']);
Route::delete('/product/{id}', [ProductController::class, 'destroy']);



Route::post('/order', [OrderController::class, 'store']);
Route::get('/order', [OrderController::class, 'index']);
Route::get('/order/{id}', [OrderController::class, 'getById']);
Route::put('/order/{id}', [OrderController::class, 'update']);