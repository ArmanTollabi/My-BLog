<?php

use App\Http\Controllers\ApiAuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {

    return response()->json([
        'user' => $request->user()
    ]);
})->middleware('auth:sanctum');


Route::get('posts',[PostController::class,'apiIndex']);
Route::get('posts/{post}',[PostController::class,'apiShow']);




Route::get('categories/',[CategoryController::class,'apiIndex']);
Route::post('categories/',[CategoryController::class,'apiStore']);
Route::get('categories/{category}',[CategoryController::class,'apiShow']);
Route::post('auth/login',[ApiAuthController::class,'apiLogin'])->middleware('throttle:login');

Route::middleware('auth:sanctum')->group(function () {

    Route::middleware('throttle:api')->group(function () {
        Route::post('logout',[ApiAuthController::class,'apiLogout']);

        Route::post('posts',[PostController::class,'apiStore']);
        Route::match(['put','patch'],'posts/{post}',[PostController::class,'apiUpdate']);
        Route::delete('posts/{post}',[PostController::class,'apiDestroy']);
    });
});



