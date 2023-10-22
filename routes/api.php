<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MobileUserController;

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

Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'loginUser']);
Route::post('/refresh',[AuthController::class,'refresh']);

Route::post('/mobile/register',[MobileUserController::class,'register']);
Route::post('/mobile/login',[MobileUserController::class,'loginUser']);
Route::post('/mobile/refresh',[MobileUserController::class,'refresh']);
