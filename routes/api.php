<?php

use App\Http\Controllers\V1\AgamaController;
use App\Http\Controllers\V1\AnggotaPasienController;
use App\Http\Controllers\V1\BantuanController;
use App\Http\Controllers\V1\DaftarAntrianController;
use App\Http\Controllers\V1\DokterController;
use App\Http\Controllers\V1\GolonganDarahController;
use App\Http\Controllers\V1\JenisIdentitasController;
use App\Http\Controllers\V1\JenisKelaminController;
use App\Http\Controllers\V1\JurusanController;
use App\Http\Controllers\V1\KamarController;
use App\Http\Controllers\V1\KecamatanController;
use App\Http\Controllers\V1\KedudukanKeluargaController;
use App\Http\Controllers\V1\KewarganegaraanController;
use App\Http\Controllers\V1\KotaKabupatenController;
use App\Http\Controllers\V1\NamaPenanggungController;
use App\Http\Controllers\V1\OtpController;
use App\Http\Controllers\V1\PasienController;
use App\Http\Controllers\V1\PasienSementaraController;
use App\Http\Controllers\V1\PenanggungController;
use App\Http\Controllers\V1\PendaftaranPoliklinikController;
use App\Http\Controllers\V1\PendidikanTerakhirController;
use App\Http\Controllers\V1\PenghasilanController;
use App\Http\Controllers\V1\PoliController;
use App\Http\Controllers\V1\ProvinsiController;
use App\Http\Controllers\V1\RiwayatPoliklinikController;
use App\Http\Controllers\V1\StatusMenikahController;
use App\Http\Controllers\V1\SukuController;
use App\Http\Controllers\V1\UploadGambarController;
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
        $this->_ambilGambar();
        $this->_pendaftaranPoliklinik();
        $this->_riwayatPoliklinik();
        $this->_penanggung();
        $this->_anggotaPasien();
        $this->_master();

        // API Pakai Token
        Route::middleware('auth:sanctum')->group(function () {
            // YOW
            Route::post(Endpoint::$LOGOUT, [UserController::class, 'logout']);
            Route::get(Endpoint::$USER, [UserController::class, 'tampilkanProfileUser']);
            Route::post(Endpoint::$DETAIL_PASIEN, [UserController::class, 'tampilkanSeluruhProfilUser']);
        });
    }
    public function v2()
    {
    }

    private function _anggotaPasien()
    {
        Route::post(Endpoint::$PENAMBAHAN_ANGGOTA_PASIEN_BARU_SEMENTARA, [PasienSementaraController::class, 'pendaftaranAnggotaPasienBaruKeTabelSementara']);
        Route::get(Endpoint::$DATA_ANGGGOTA_PASIEN, [AnggotaPasienController::class, 'getAnggotaIndukPasien']);
        Route::get(Endpoint::$DETAIL_DATA_ANGGOTA_PASIEN, [AnggotaPasienController::class, 'getDetailAnggotaIndukPasien']);
    }

    private function _penanggung()
    {
        Route::get(Endpoint::$LIST_PENANGGUNG, [PenanggungController::class, 'listPenanggung']);
        Route::get(Endpoint::$CEK_PENANGGUNG, [PenanggungController::class, 'validasiPenanggung']);
        Route::post(Endpoint::$TAMBAH_PENANGGUNG, [PenanggungController::class, 'tambahPenanggung']);
        Route::delete(Endpoint::$HAPUS_PENANGGUNG, [PenanggungController::class, 'hapusPenanggung']);
    }

    private function _riwayatPoliklinik()
    {
        Route::get(Endpoint::$RIWAYAT_POLIKLINIK, [RiwayatPoliklinikController::class, 'getRiwayatPoliklinik']);
    }

    private function _pendaftaranPoliklinik()
    {
        Route::get(Endpoint::$DATA_PASIEN, [PendaftaranPoliklinikController::class, 'getNomorRM']);
        Route::get(Endpoint::$HARI_BEROBAT, [PendaftaranPoliklinikController::class, 'getHariBerobat']);
        Route::get(Endpoint::$DEBITUR, [PendaftaranPoliklinikController::class, 'getDebitur']);
        Route::post(Endpoint::$PENDAFTARAN_POLIKLINIK, [PendaftaranPoliklinikController::class, 'daftarPoliklinik']);
        Route::get(Endpoint::$LIST_PENDAFTARAN_POLIKLINIK, [PendaftaranPoliklinikController::class, 'getPendaftaranPoliklinik']);
        Route::post(Endpoint::$UBAH_STATUS_PENDAFTARAN, [PendaftaranPoliklinikController::class, 'ubahStatusPendaftaran']);
        Route::post(Endpoint::$SELESAI_PENDAFTARAN, [PendaftaranPoliklinikController::class, 'selesaiPendaftaran']);
    }

    private function _auth()
    {
        Route::post(Endpoint::$REGISTER, [UserController::class, 'register']);
        Route::post(Endpoint::$LOGIN, [UserController::class, 'login']);
        Route::post(Endpoint::$CEK_PASSWORD_GANTI_PASSWORD, [UserController::class, 'cekPasswordGantiPassword']);
        Route::post(Endpoint::$GANTI_PASSWORD, [UserController::class, 'gantiPassword']);
        
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
        Route::post(Endpoint::$DAFTAR_PASIEN_BARU_KE_TABEL_SEMENTARA, [PasienSementaraController::class, 'pendaftaranPasienBaruKeTabelSementara']);
        Route::post(Endpoint::$DAPAT_OTP_PENDAFTARAN_AKUN, [PasienController::class, 'dapatkanKodeOtpPendaftaranAKun']);
        Route::post(Endpoint::$KONFIRMASI_OTP_PENDAFTARAN_AKUN, [PasienController::class, 'konfirmasiKodeOtpPendaftaranAkun']);
    }

    private function _ambilGambar()
    {
        Route::post(Endpoint::$AMBIL_GAMBAR, [UploadGambarController::class, 'trollGambar']);
    }

    private function _master()
    {
        Route::get(Endpoint::$PROVINSI, [ProvinsiController::class, 'getAllProvinsi']);
        Route::get(Endpoint::$KABUPATEN_KOTA, [KotaKabupatenController::class, 'getKabupatenKota']);
        Route::get(Endpoint::$KECAMATAN, [KecamatanController::class, 'getKecamatan']);
        Route::get(Endpoint::$AGAMA, [AgamaController::class, 'getAgama']);
        Route::get(Endpoint::$GOLONGAN_DARAH, [GolonganDarahController::class, 'getGolonganDarah']);
        Route::get(Endpoint::$KEDUDUKAN_KELUARGA, [KedudukanKeluargaController::class, 'getKedudukanKeluarga']);
        Route::get(Endpoint::$SUKU, [SukuController::class, 'getSuku']);
        Route::get(Endpoint::$PENDIDIKAN_TERAKHIR, [PendidikanTerakhirController::class, 'getPendidikanTerakhir']);
        Route::get(Endpoint::$PENGHASILAN, [PenghasilanController::class, 'getPenghasilan']);
        Route::get(Endpoint::$NAMA_PENANGGUNG, [NamaPenanggungController::class, 'getNamaPenanggung']);
        Route::get(Endpoint::$JURUSAN, [JurusanController::class, 'getJurusan']);
        Route::get(Endpoint::$STATUS_MENIKAH, [StatusMenikahController::class, 'getStatusMenikah']);
        Route::get(Endpoint::$KEWARGANEGARAAN, [KewarganegaraanController::class, 'getKewarganegaraan']);
        Route::get(Endpoint::$JENIS_IDENTIAS, [JenisIdentitasController::class, 'getJenisIdentitas']);
        Route::get(Endpoint::$JENIS_KELAMIN, [JenisKelaminController::class, 'getJenisKelamin']);
        Route::get(Endpoint::$POLI, [PoliController::class, 'getPoli']);
        Route::get(Endpoint::$LIST_DOKTER_POLI, [DokterController::class, 'getDokterPerPoli']);
        Route::get(Endpoint::$LIST_KAMAR, [KamarController::class, 'getListKamar']);
        Route::get(Endpoint::$LIST_DAFTAR_ANTRIAN, [DaftarAntrianController::class, 'getDaftarAntrian']);
        Route::get(Endpoint::$DETAIL_KAMAR, [KamarController::class, 'getDetailKamar']);
        Route::get(Endpoint::$BANTUAN, [BantuanController::class, 'getBantuan']);
        Route::get(Endpoint::$ANTRIAN_DETAIL_PENDAFTARAN, [DaftarAntrianController::class, 'getDetailListAntrian']);
        Route::get(Endpoint::$ANTRIAN_DETAIL_POLIKLINIK, [DaftarAntrianController::class, 'getDaftarAntrianPoliklinik']);
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
    static $AMBIL_GAMBAR = 'ambil-gambar';
    static $PROVINSI = 'provinsi';
    static $KABUPATEN_KOTA = 'kabupaten-kota';
    static $KECAMATAN = 'kecamatan';
    static $AGAMA = 'agama';
    static $GOLONGAN_DARAH = 'golongan-darah';
    static $KEDUDUKAN_KELUARGA = 'kedudukan-keluarga';
    static $SUKU = 'suku';
    static $PENDIDIKAN_TERAKHIR = 'pendidikan-terakhir';
    static $PENGHASILAN = 'penghasilan';
    static $NAMA_PENANGGUNG = 'nama-penanggung';
    static $JURUSAN = 'jurusan';
    static $STATUS_MENIKAH = 'status-menikah';
    static $KEWARGANEGARAAN = 'kewarganegaraan';
    static $JENIS_IDENTIAS = 'jenis-identitas';
    static $JENIS_KELAMIN = 'jenis-kelamin';
    static $DAPAT_OTP_PENDAFTARAN_AKUN = 'dapat-otp-pendaftaran-akun';
    static $KONFIRMASI_OTP_PENDAFTARAN_AKUN= 'konfirmasi-otp-pendaftaran-akun';
    static $CEK_PASSWORD_GANTI_PASSWORD = 'cek-password-ganti-password';
    static $GANTI_PASSWORD = 'ganti-password';
    static $DAFTAR_PASIEN_BARU_KE_TABEL_SEMENTARA = 'pendaftaran-pasien-baru-sementara';
    static $POLI = 'poli';
    static $LIST_DOKTER_POLI = 'get-dokter-poliklinik';
    static $LIST_KAMAR = 'list-kamar';
    static $LIST_DAFTAR_ANTRIAN = 'list-daftar-antrian';
    static $DETAIL_PASIEN = 'get-seluruh-data-akun-pasien';
    static $DETAIL_KAMAR = 'detail-kamar';
    static $BANTUAN = 'bantuan';
    static $ANTRIAN_DETAIL_PENDAFTARAN = 'list-daftar-antrian-detail';
    static $ANTRIAN_DETAIL_POLIKLINIK = 'list-daftar-antrian-detail-poliklinik';
    static $DATA_PASIEN = 'data-akun';
    static $HARI_BEROBAT = 'hari-berobat';
    static $DEBITUR = 'get-penanggung';
    static $PENDAFTARAN_POLIKLINIK = 'daftar-poliklinik';
    static $LIST_PENDAFTARAN_POLIKLINIK = 'list-pendaftaran-poliklinik';
    static $UBAH_STATUS_PENDAFTARAN = 'ubah-status-pendaftaran';
    static $SELESAI_PENDAFTARAN = 'selesai-pendaftaran';
    static $RIWAYAT_POLIKLINIK = 'riwayat-poliklinik-pasien';
    static $LIST_PENANGGUNG = 'list-penanggung';
    static $CEK_PENANGGUNG = 'cek-penanggung';
    static $TAMBAH_PENANGGUNG = 'tambah-penanggung';
    static $HAPUS_PENANGGUNG = 'hapus-penanggung';
    static $PENAMBAHAN_ANGGOTA_PASIEN_BARU_SEMENTARA = 'pendaftaran-anggota-pasien-baru-sementara';
    static $DATA_ANGGGOTA_PASIEN = 'get-data-anggota-pasien';
    static $DETAIL_DATA_ANGGOTA_PASIEN = 'get-detail-data-anggota-pasien';
    // Isi Lagi Endpoint nya cuk
}
