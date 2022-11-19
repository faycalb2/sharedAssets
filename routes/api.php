<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// Public
Route::post('register', [RegisterController::class, 'storeAdmin']);
Route::post('login', [LoginController::class, 'login']);

// Auth
Route::group(['middleware' => ['auth:sanctum']], function(){
    Route::post('user/register', [RegisterController::class, 'storeUser']);
    Route::post('logout', [LogoutController::class, 'destroy']);
    Route::resource('assets', AssetController::class);
    Route::resource('teams', TeamController::class);
    Route::resource('tags', TagController::class);
});