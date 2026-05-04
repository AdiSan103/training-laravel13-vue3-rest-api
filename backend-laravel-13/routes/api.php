<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Semua route di sini otomatis mendapat prefix /api
| Contoh: Route::post('/login') → bisa diakses di http://localhost:8000/api/login
|
| Route dibagi 2 grup:
| 1. Public   → bisa diakses tanpa token (login, register)
| 2. Protected → wajib membawa JWT token di header Authorization
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Public Routes (tidak butuh token)
|--------------------------------------------------------------------------
*/

// Endpoint untuk login — mengembalikan JWT token jika berhasil
Route::post('/login', [AuthController::class, 'login']);

// Endpoint untuk registrasi user baru
Route::post('/register', [AuthController::class, 'register']);

/*
|--------------------------------------------------------------------------
| Protected Routes (wajib membawa JWT token)
|--------------------------------------------------------------------------
| Semua route di dalam group ini dilindungi middleware auth:api.
| Client wajib mengirim header:
|   Authorization: Bearer {token}
|
| Jika token tidak ada / expired / tidak valid → otomatis return 401
|--------------------------------------------------------------------------
*/
Route::middleware('auth:api')->group(function () {

    // Mengambil data user yang sedang login berdasarkan token
    Route::get('/me', [AuthController::class, 'me']);

    // Logout — menginvalidasi token agar tidak bisa dipakai lagi
    Route::post('/logout', [AuthController::class, 'logout']);

    // Update profil user (name, password, avatar)
    // Pakai POST karena mengupdate resource yang sudah ada, tapi bisa juga pakai PUT/PATCH
    Route::post('/update-profile', [AuthController::class, 'updateProfile']);

    /*
    | apiResource otomatis membuat 5 route sekaligus:
    |
    | GET    /products         → index()   — ambil semua produk
    | POST   /products         → store()   — buat produk baru
    | GET    /products/{id}    → show()    — ambil detail produk
    | PUT    /products/{id}    → update()  — update produk
    | DELETE /products/{id}    → destroy() — hapus produk
    */
    Route::apiResource('products', ProductController::class);
});
