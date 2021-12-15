<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogicSystemController;
use App\Http\Controllers\AuthController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware('api','auth:api')->group(function () { 

    Route::post('register_client', [LogicSystemController::class, 'register_client']);
    Route::post('recharge_wallet', [LogicSystemController::class, 'recharge_wallet']);
    Route::post('payment', [LogicSystemController::class, 'payment']);  
    Route::post('consult', [LogicSystemController::class, 'consult']);  
});
Route::get('confirm_payment/{token}/{price}', [LogicSystemController::class, 'confirm_payment'])->name('confirm_payment');
Route::post('login_token', [AuthController::class, 'login_token']);