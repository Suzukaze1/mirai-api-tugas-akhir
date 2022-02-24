<?php

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

Route::get('/', [LoginViewController::class,'index']);
Route::post('/login', [LoginViewController::class, 'login']);
Route::get('/logout', [LoginViewController::class, 'logout']);

Route::get('/home', [LoginViewController::class, 'home'])->middleware('CekLogin');
Route::get('/list-pasien', [LoginViewController::class, 'listPasien'])->middleware('CekLogin');
Route::get('/list-pasien/validasi/{id}', [LoginViewController::class, 'validasiPasien'])->middleware('CekLogin');
Route::post('/list-pasien/validasi/verifikasi', [LoginViewController::class, 'verifikasiPasien'])->middleware('CekLogin');