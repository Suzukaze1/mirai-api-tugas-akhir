<?php

use App\Helpers\FormRiwayatPoliklinik;
use App\Http\Controllers\View\LoginViewController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

//dummy
Route::get('/test', [FormRiwayatPoliklinik::class, 'contohFormRiwayat']);

Route::get('/', [LoginViewController::class,'index']);
Route::post('/login', [LoginViewController::class, 'login']);
Route::get('/logout', [LoginViewController::class, 'logout']);

Route::get('/home', [LoginViewController::class, 'home'])->middleware('CekLogin');
Route::get('/list-pasien-baru', [LoginViewController::class, 'listPasien'])->middleware('CekLogin');
Route::get('/list-pasien/validasi/{id}', [LoginViewController::class, 'validasiPasien'])->middleware('CekLogin');
Route::post('/list-pasien/validasi/verifikasi', [LoginViewController::class, 'verifikasiPasien'])->middleware('CekLogin');
Route::get('/list-pasien-lama', [LoginViewController::class, 'listPasienLama'])->middleware('CekLogin');
Route::get('/list-pasien-lama/validasi/{id}', [LoginViewController::class, 'validasiPasienLama'])->middleware('CekLogin');
Route::post('/list-pasien-lama/validasi/verifikasi', [LoginViewController::class, 'verifikasiPasienLama'])->middleware('CekLogin');

// anggota
Route::get('/list-anggota-pasien-lama', [LoginViewController::class, 'listAnggotaPasienLama'])->middleware('CekLogin');
Route::get('/list-anggota-pasien-lama/validasi/{id}', [LoginViewController::class, 'validasiAnggotaPasienLama'])->middleware('CekLogin');
Route::post('/list-anggota-pasien-lama/validasi/verifikasi', [LoginViewController::class, 'verifikasiAnggotaPasienLama'])->middleware('CekLogin');

Route::get('/list-anggota-pasien-baru', [LoginViewController::class, 'listAnggotaPasienBaru'])->middleware('CekLogin');
Route::get('/list-anggota-pasien-baru/validasi/{id}', [LoginViewController::class, 'validasiAnggotaPasienBaru'])->middleware('CekLogin');
Route::post('/list-anggota-pasien-baru/validasi/verifikasi', [LoginViewController::class, 'verifikasiAnggotaPasienBaru'])->middleware('CekLogin');