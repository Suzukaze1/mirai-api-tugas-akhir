<?php

namespace App\Http\Controllers\V1;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\V1\Otp;
use App\Models\V1\Suku;
use App\Models\V1\Agama;
use App\Models\V1\Pasien;
use App\Models\V1\Jurusan;
use App\Models\V1\Provinsi;
use App\Models\V1\Kecamatan;
use Illuminate\Http\Request;
use App\Models\V1\Penghasilan;
use App\Models\V1\JenisKelamin;
use App\Models\V1\GolonganDarah;
use App\Models\V1\KotaKabupaten;
use App\Models\V1\StatusMenikah;
use App\Helpers\ResponseFormatter;
use App\Models\V1\jenis_identitas;
use App\Models\V1\Kewarganegaraan;
use App\Models\V1\PasienSementara;
use App\Http\Controllers\Controller;
use App\Models\V1\KedudukanKeluarga;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\V1\PendidikanTerakhir;

class UserController extends Controller
{
    public function register(Request $request)
    {
        return "KLLELE";
        die();
        try{
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string']
            ]);

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            $user = User::where('email', $request->email)->first();

            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return ResponseFormatter::success_ok([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'User Registered');

        }catch (Exception $e){
            return ResponseFormatter::error_not_found([
                'message' => 'something went wrong',
                'error' => $e
            ], 'Authentication Failed', 500);
        }
    }

    public function login(Request $request)
    {
        $getTime = Carbon::now()->addHour(10);
        $exp_time = $getTime->format('Y-m-d H:i:s');
        try {
            $request->validate([
                'email' => 'email|required',
                'password' => 'required'
            ]);
    
            $credentials = request(['email', 'password']);
            if(!Auth::attempt($credentials)){
                return ResponseFormatter::forbidden(
                    'Password Salah',
                    null
                );
            }
    
            $user = User::where('email', $request->email)->first();

            if($user->kode == NULL){
                $kode_rm = null;
            }else{
                $kode_rm = sprintf("%08s", strval($user->kode));
            }

            if(!Hash::check($request->password, $user->password, [])){
                throw new \Exception('Invalid Credentials');
            }

            $tokenResult = $user->createToken('authToken')->plainTextToken;

            if(!$user->kode == null) $status = true;
            if($user->kode == null) $status = false;

            $response = [];
            $response['id'] = $user->id;
            $response['email'] = $user->email;
            $response['password'] = $user->password;
            $response['nama'] = $user->name;
            $response['nomor_rekam_medis'] = $kode_rm;
            $response['access_token'] = $tokenResult;
            $response['status_validasi'] = $status;
            $response['token_expired'] = $exp_time;

            return ResponseFormatter::success_ok(
                'Berhasil Login', $response);
        } catch (Exception $e) {
            return ResponseFormatter::internal_server_error(
                'Kesalahan Pada Server',
                $e
            );
        }
    }

    public function tampilkanProfileUser(Request $request)
    {
        return ResponseFormatter::success_ok(
            'Data Profile User Berhasil Diambil',
            $request->user()
        );
    }

    public function cekValidasiAkunPasien(Request $request)
    {
        $adadasd = $request->id;
        if($adadasd == null){
            return "Test";
        }
    }

    public function tampilkanSeluruhProfilUser(Request $request)
    {
        try{
            $email = $request->email;

            //$user = User::where('id', $id)->where('email', $email)->first();
            $user = User::where('email', $email)->first();
            if($user == null) return ResponseFormatter::error_not_found("Data Tidak Ditemukan", null);
            
            if($user->kode == NULL){
                $message = "Berhasil Mendapatkan Data Pasien Sementara";
                $pasien = PasienSementara::where('id', $user->id_pasien_temp)->first();

                //fetching
                $agama_kode = Agama::where('kode', $pasien->agama_kode)->first();
                $pendidikan_kode = PendidikanTerakhir::where('kode', $pasien->pendidikan_kode)->first();
                $kewarganegaraan_kode1 = Kewarganegaraan::where('kode', $pasien->kewarganegaraan_kode)->first();
                $jenis_identitas_kode = jenis_identitas::where('kode', $pasien->jenis_identitas_kode)->first();
                $suku_kode = Suku::where('kode', $pasien->suku_kode)->first();
                $jenis_kelamin_kode = JenisKelamin::where('kode', $pasien->jkel)->first();
                $status_perkawinan_kode = StatusMenikah::where('kode', $pasien->status_perkawinan)->first();
                $kedudukan_keluarga_kode = KedudukanKeluarga::where('kode', $pasien->kedudukan_keluarga)->first();
                $golongan_darah_kode = GolonganDarah::where('kode', $pasien->golongan_darah)->first();
                $provinsi_kode = Provinsi::where('kode', $pasien->provinsi)->first();
                $kabupaten_kode = KotaKabupaten::where('kode', $pasien->kabupaten)->first();
                $kecamatan_kode = Kecamatan::where('kode', $pasien->kecamatan)->first();
                $jurusan_kode = Jurusan::where('kode', $pasien->jurusan)->first();
                $penghasilan_kode = Penghasilan::where('kode', $pasien->penghasilan)->first();

                // value
                $rekam_medis = null;
                $nomor_identitas = $pasien->no_identitas;
                $jenis_identitas = $jenis_identitas_kode->nama;
                $nama_lengkap = $pasien->nama;
                $tempat_lahir = $pasien->tempat_lahir;
                $tanggal_lahir = $pasien->tanggal_lahir;
                $kedudukan_keluarga = $kedudukan_keluarga_kode->nama;
                $golongan_darah = $golongan_darah_kode->nama;
                $agama = $agama_kode->agama;
                $suku = $suku_kode->nama;
                $nomor_telepon = $pasien->no_telp;
                $jenis_kelamin = $jenis_kelamin_kode->nama;
                $alamat = $pasien->alamat;
                $provinsi = $provinsi_kode->nama;
                $kota_kabupaten = $kabupaten_kode->nama;
                $kecamatan = $kecamatan_kode->nama;
                $status_perkawinan = $status_perkawinan_kode->nama;
                $umur = $pasien->umur;
                $anak_ke = $pasien->anak_ke;
                $pendidikan_terakhir = $pendidikan_kode->nama;
                $jurusan = $jurusan_kode->nama;
                $nama_tempat_bekerja = $pasien->nama_tempat_bekerja;
                $alamat_tempat_bekerja = $pasien->alamat_tempat_bekerja;
                $penghasilan = $penghasilan_kode->nama;
                $pekerjaan_kode = $pasien->pekerjaan_kode;
                $kewarganegaraan_kode = $kewarganegaraan_kode1->nama;
                $nama_pasangan = $pasien->nama_pasangan;
                $nama_ayah = $pasien->ayah_nama;
                $nomor_rekam_medis_ayah = $pasien->no_rekam_medis_ayah;
                $nama_ibu = $pasien->ibu_nama;
                $nomor_rekam_medis_ibu = $pasien->no_rekam_medis_ibu;
                $alergi = $pasien->alergi;

            }else if ($user->id_pasien_temp == null){
                $kode_rm = sprintf("%08s", strval($user->kode));
                $message = "Berhasil Mendapatkan Data Pasien";
                $pasien = Pasien::where('kode', $kode_rm)->first();

                //fetching
                $agama_kode = Agama::where('kode', $pasien->agama_kode)->first();
                $pendidikan_kode = PendidikanTerakhir::where('kode', $pasien->pendidikan_kode)->first();
                $kewarganegaraan_kode1 = Kewarganegaraan::where('kode', $pasien->kewarganegaraan_kode)->first();
                $jenis_identitas_kode = jenis_identitas::where('kode', $pasien->jenis_identitas_kode)->first();
                $suku_kode = Suku::where('kode', $pasien->suku_kode)->first();
                $jenis_kelamin_kode = JenisKelamin::where('kode', $pasien->jkel)->first();
                $status_perkawinan_kode = StatusMenikah::where('kode', $pasien->status_perkawinan)->first();
                $kedudukan_keluarga_kode = KedudukanKeluarga::where('kode', $pasien->kedudukan_keluarga)->first();
                $golongan_darah_kode = GolonganDarah::where('kode', $pasien->golongan_darah)->first();
                $provinsi_kode = Provinsi::where('kode', $pasien->provinsi)->first();
                $kabupaten_kode = KotaKabupaten::where('kode', $pasien->kabupaten)->first();
                $kecamatan_kode = Kecamatan::where('kode', $pasien->kecamatan)->first();
                $jurusan_kode = Jurusan::where('kode', $pasien->jurusan)->first();
                $penghasilan_kode = Penghasilan::where('kode', $pasien->penghasilan)->first();

                // value
                $rekam_medis = sprintf("%08s", strval($pasien->kode));
                $nomor_identitas = $pasien->no_identitas;
                $jenis_identitas = $jenis_identitas_kode->nama;
                $nama_lengkap = $pasien->nama;
                $tempat_lahir = $pasien->tempat_lahir;
                $tanggal_lahir = $pasien->tanggal_lahir;
                $kedudukan_keluarga = $kedudukan_keluarga_kode->nama;
                $golongan_darah = $golongan_darah_kode->nama;
                $agama = $agama_kode->agama;
                $suku = $suku_kode->nama;
                $nomor_telepon = $pasien->no_telp;
                $jenis_kelamin = $jenis_kelamin_kode->nama;
                $alamat = $pasien->alamat;
                $provinsi = $provinsi_kode->nama;
                $kota_kabupaten = $kabupaten_kode->nama;
                $kecamatan = $kecamatan_kode->nama;
                $status_perkawinan = $status_perkawinan_kode->nama;
                $umur = $pasien->umur;
                $anak_ke = $pasien->anak_ke;
                $pendidikan_terakhir = $pendidikan_kode->nama;
                $jurusan = $jurusan_kode->nama;
                $nama_tempat_bekerja = $pasien->nama_tempat_bekerja;
                $alamat_tempat_bekerja = $pasien->alamat_tempat_bekerja;
                $penghasilan = $penghasilan_kode->nama;
                $pekerjaan_kode = $pasien->pekerjaan_kode;
                $kewarganegaraan_kode = $kewarganegaraan_kode1->nama;
                $nama_pasangan = $pasien->nama_pasangan;
                $nama_ayah = $pasien->ayah_nama;
                $nomor_rekam_medis_ayah = $pasien->no_rekam_medis_ayah;
                $nama_ibu = $pasien->ibu_nama;
                $nomor_rekam_medis_ibu = $pasien->no_rekam_medis_ibu;
                $alergi = $pasien->alergi;
            }else{
                return ResponseFormatter::error_not_found("Pasien Error", null);
            }

            $response = [];
            $response['nomor_rekam_medis'] = $rekam_medis;
            $response['nomor_identitas'] = $nomor_identitas;
            $response['jenis_identitas'] = $jenis_identitas;
            $response['nama_lengkap'] = $nama_lengkap;
            $response['tempat_lahir'] = $tempat_lahir;
            $response['tanggal_lahir'] = $tanggal_lahir;
            $response['kedudukan_keluarga'] = $kedudukan_keluarga;
            $response['golongan_darah'] = $golongan_darah;
            $response['agama'] = $agama;
            $response['suku'] = $suku;
            $response['nomor_telepon'] = $nomor_telepon;
            $response['jenis_kelamin'] = $jenis_kelamin;
            $response['alamat'] = $alamat;
            $response['provinsi'] = $provinsi;
            $response['kota_kabupaten'] = $kota_kabupaten;
            $response['kecamatan'] = $kecamatan;
            $response['status_perkawinan'] = $status_perkawinan;
            $response['umur'] = $umur;
            $response['anak_ke'] = $anak_ke;
            $response['pendidikan_terakhir'] = $pendidikan_terakhir;
            $response['jurusan'] = $jurusan;
            $response['nama_tempat_bekerja'] = $nama_tempat_bekerja;
            $response['alamat_tempat_bekerja'] = $alamat_tempat_bekerja;
            $response['penghasilan'] = $penghasilan;
            $response['pekerjaan_kode'] = $pekerjaan_kode;
            $response['kewarganegaraan_kode'] = $kewarganegaraan_kode;
            $response['nama_pasangan'] = $nama_pasangan;
            $response['nama_ayah'] = $nama_ayah;
            $response['nomor_rekam_medis_ayah'] = $nomor_rekam_medis_ayah;
            $response['nama_ibu'] = $nama_ibu;
            $response['nomor_rekam_medis_ibu'] = $nomor_rekam_medis_ibu;
            $response['alergi'] = $alergi;
            return ResponseFormatter::success_ok($message, $response);
        }catch (Exception $e){
            return ResponseFormatter::internal_server_error('Error dari Server', $e);
        } 
    }

    public function lupaPassword(Request $request)
    {
        try{
            $email = $request->email;
            $password = $request->password;
            $password_hash = Hash::make($password);

            // cek email di table user
            $otp1 = user::where('email', $email)->first();

            // cek email di table otp
            $otp_email = otp::where('email', $email)->first();

            $update_akun_saya = User::find($otp1->id);
            
            $update_akun_saya->email = $email;
            $update_akun_saya->password = $password_hash;

            $response = [];
            $response['email'] = $email;
            $response['password'] = $password;

            $otp = Otp::find($otp_email->id);

            if($otp){
                $otp->delete();
                $update_akun_saya->save();

                return ResponseFormatter::success_ok(
                    'Berhasil Dihapus OTP dan Mengubah Data di profil',
                    $response
                );
            }else{
                return ResponseFormatter::error_not_found(
                    'Tidak Ditemukan',
                    null
                );
            }
        }catch (Exception $e){
            return ResponseFormatter::internal_server_error(
                'Kesalahan Pada Server',
                $e
            );
        }
        
    }

    public function cekPasswordGantiPassword(Request $request)
    {
        try{
            $email = $request->email;
            $password = $request->password;

            $response = [];
            $response['email'] = $email;
            $response['password'] = $password;

            if(!$user = User::where('email', $email)->first()) return ResponseFormatter::error_not_found("Email Tidak Ditemukan", null);
            if(!Hash::check($password, $user->password, [])) return ResponseFormatter::error_not_found("Password Salah", null);
            return ResponseFormatter::success_ok("Password Benar", $response);
        }catch (Exception $e){
            return ResponseFormatter::internal_server_error("Ada Yang Salah Pada Server", $e);
        }
    }

    public function gantiPassword(Request $request)
    {
        try{
            $email = $request->email;
            $password_lama = $request->password_lama;
            $password_baru = $request->password_baru;
            $ulangi_password_baru = $request->ulangi_password_baru;

            $response = [];
            $response['email'] = $email;
            $response['password_lama'] = $password_lama;
            $response['password_baru'] = $password_baru;
            $response['ulangi_password_baru'] = $ulangi_password_baru;

            if(!$user = User::where('email', $email)->first()) return ResponseFormatter::error_not_found("Email Tidak Ditemukan", null);
            if(!Hash::check($password_lama, $user->password, [])) return ResponseFormatter::error_not_found("Password Salah", null);
            if($password_baru != $ulangi_password_baru) return ResponseFormatter::error_not_found("Password Baru Tidak Sama", null);

            $ganti_password = User::find($user->id);
            $ganti_password->password = Hash::make($password_baru);
            $ganti_password->save();

            return ResponseFormatter::success_ok("Berhasil Ganti Password", $response);
        }catch (Exception $e){
            return ResponseFormatter::internal_server_error("Ada Yang Salah Pada Server", $e);
        }
    }

    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken()->delete();

        return ResponseFormatter::success_ok('Token Revoked/Dihapus', $token);
    }
}
