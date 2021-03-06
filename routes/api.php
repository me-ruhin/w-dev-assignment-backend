<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Models\Delivery;
use App\Models\OrderMaster;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('v1/register', [AuthController::class, 'register']);
Route::post('v1/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum', 'isAdmin'], 'prefix' => "v1/admin"], function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::apiResource('products', ProductController::class);
    Route::post('/modify/orders/status/{reference_no}', [OrderController::class, 'modifyExistingOrderStatus']);
    Route::get('orders', [OrderController::class, 'index']);

});


Route::group(['middleware' => ['auth:sanctum'], 'prefix' => "v1"], function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('orders', [OrderController::class, 'addOrders']);
    Route::post('modify/orders/{reference_no}', [OrderController::class, 'modifyExistingOrder']);
});

