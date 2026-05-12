<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostController;
use App\Http\Middleware\CheckToken;
use Illuminate\Support\Facades\Route;

// Public
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Protected
Route::middleware(CheckToken::class)->group(function () {
    Route::get('/me',      [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Posts
    Route::get('/posts',               [PostController::class, 'index']);
    Route::post('/posts',              [PostController::class, 'store']);
    Route::get('/posts/{slug}',        [PostController::class, 'show']);
    Route::post('/posts/{slug}/edit',  [PostController::class, 'update']);
    Route::post('/posts/{slug}/delete',[PostController::class, 'destroy']);

    // Categories
    Route::get('/categories',          [CategoryController::class, 'index']);
    Route::post('/categories',         [CategoryController::class, 'store']);
    Route::post('/categories/{id}/edit',   [CategoryController::class, 'update']);
    Route::post('/categories/{id}/delete', [CategoryController::class, 'destroy']);
});
