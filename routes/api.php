<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;

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

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => "v1"], function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('/products', [ProductController::class, 'getProducts']);
    Route::post('/products', [ProductController::class, 'storeProduct']);
    Route::put('/update/products', [ProductController::class, 'updateProduct']);
    Route::delete('/products/{id}', [ProductController::class, 'deleteProduct']);
    Route::get('products/{keyword}',[ProductController::class, 'searchByKeword']);
});
