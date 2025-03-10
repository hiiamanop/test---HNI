<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

// Public route
Route::post('login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('jwt.auth')->group(function () {
    Route::get('users', [UserController::class, 'index']);
    Route::post('users', [UserController::class, 'store']);
    Route::get('users/{id}', [UserController::class, 'show']);
    Route::delete('users/{id}', [UserController::class, 'destroy']);
});