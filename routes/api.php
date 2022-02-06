<?php

use App\Http\Controllers\V1\OtpController;
use App\Http\Controllers\V1\PasienController;
use App\Http\Controllers\V1\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// API V1 
Route::prefix('v1')->group(function () {
    // API AUTH
    Route::post('register', [UserController::class, 'register']);
    Route::post('login', [UserController::class, 'login']);
    
    // Api OTP Lupa Password
    Route::post('dapat-otp-lupa-password', [OtpController::class, 'dapatkanKodeOtpLupaPassword']);
    Route::post('konfirmasi-dapat-otp-lupa-password', [OtpController::class, 'konfirmasiKodeOtpLupaPassword']);
    Route::post('lupa-password', [UserController::class, 'lupaPassword']);

    // API Daftar Pasien
    Route::post('pendaftaran-pasien-baru', [PasienController::class, 'pendaftaranPasienBaru']);

    // API Pakai Token
    Route::middleware('auth:sanctum')->group(function (){
        Route::post('logout', [UserController::class, 'logout']);
        Route::get('user', [UserController::class, 'tampilkanProfileUser']);
    });

    
});