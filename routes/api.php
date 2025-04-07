<?php

use App\Http\Controllers\AlbumController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // Albums
    Route::get('/albums', [AlbumController::class, 'index']);
    Route::post('/albums/{album}/vote', [AlbumController::class, 'vote']);
    Route::delete('/albums/{album}', [AlbumController::class, 'destroy']);
});

