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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// API V1.0.0 
Route::prefix('v1')->group(function () {
    $routes = new Routes();
    $routes->v1();
});
Route::prefix('v2')->group(function () {
    // Routes::v1();
});

// Pakai kek gini bisa ga cuk??
// Coba di organize route nya kek gini cuk biar rapi 
class Routes
{

    public function v1()
    {
        $this->_auth();
        $this->_otpLupaPassword();
        $this->_daftarPasien();

        // API Pakai Token
        Route::middleware('auth:sanctum')->group(function () {
            // YOW
            Route::post(Endpoint::$LOGOUT, [UserController::class, 'logout']);
            Route::get(Endpoint::$USER, [UserController::class, 'tampilkanProfileUser']);
        });
    }
    public function v2()
    {
    }

    private function _auth()
    {
        Route::post(Endpoint::$REGISTER, [UserController::class, 'register']);
        Route::post(Endpoint::$LOGIN, [UserController::class, 'login']);
    }

    private function _otpLupaPassword()
    {
        Route::post(Endpoint::$DAPAT_OTP_LUPA_PASSWORD, [OtpController::class, 'dapatkanKodeOtpLupaPassword']);
        Route::post(Endpoint::$KONFIRMASI_DAPAT_OTP_LUPA_PASSWORD, [OtpController::class, 'konfirmasiKodeOtpLupaPassword']);
        Route::post(Endpoint::$LUPA_PASSWORD, [UserController::class, 'lupaPassword']);
    }

    private function _daftarPasien()
    {
        Route::post(Endpoint::$PENDAFTARAN_PASIEN_BARU, [PasienController::class, 'pendaftaranPasienBaru']);
        Route::post(Endpoint::$PENDAFTARAN_PASIEN_LAMA, [PasienController::class, 'pendaftaranPasienLama']);
    }
}

class Endpoint
{
    static $REGISTER = 'register';
    static $LOGIN = 'login';
    static $LOGOUT = 'logout';
    static $USER = 'user';
    static $DAPAT_OTP_LUPA_PASSWORD = 'dapat-otp-lupa-password';
    static $KONFIRMASI_DAPAT_OTP_LUPA_PASSWORD = 'konfirmasi-dapat-otp-lupa-password';
    static $LUPA_PASSWORD = 'lupa-password';
    static $PENDAFTARAN_PASIEN_BARU = 'pendaftaran-pasien-baru';
    static $PENDAFTARAN_PASIEN_LAMA = 'pendaftaran-pasien-lama';
    // Isi Lagi Endpoint nya cuk
}
