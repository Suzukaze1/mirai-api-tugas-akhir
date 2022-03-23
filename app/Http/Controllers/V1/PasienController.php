<?php

namespace App\Http\Controllers\V1;

use stdClass;
use Exception;
use App\Models\User;
use App\Helpers\Foto;
use App\Models\V1\Otp;
use App\Mail\MyTestMail;
use App\Helpers\Constant;
use App\Models\V1\Pasien;
use Illuminate\Http\Request;
use App\Models\V1\DetailAkun;
use App\Models\V1\FotoPasien;
use App\Models\V1\Penanggung;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Facade\FlareClient\Http\Response;
use Illuminate\Support\Facades\Password;

class PasienController extends Controller
{
    public function pendaftaranPasienBaru(Request $request)
    {
        // logika nomor rekam medik
        $cek_pasien = Pasien::orderBy('kode', 'DESC')->first();

        $kode_rm = 0;
        if ($cek_pasien == null) {
            $kode_rm = 1;
        } else {
            if ($cek_pasien->kode == null) {
                $kode_rm = 1;
            } else if ($cek_pasien->kode >= 1) {
                $kode_rm = (int)$cek_pasien->kode + 1;
            }
        }

        // nomor rekam medik
        $nomor_rekam_medik = sprintf("%08s", strval($kode_rm));

        // get value text
        $nomor_identitas = $request->nomor_identitas;
        $jenis_identitas = $request->jenis_identitas;
        $nama_lengkap = $request->nama_lengkap;
        $tempat_lahir = $request->tempat_lahir;
        $tanggal_lahir = $request->tanggal_lahir;
        $kedudukan_keluarga = $request->kedudukan_keluarga;
        $golongan_darah = $request->golongan_darah;
        $agama = $request->agama;
        $suku = $request->suku;
        $nomor_telepon = $request->nomor_telepon;
        $jenis_kelamin = $request->jenis_kelamin;
        $alamat = $request->alamat;
        $provinsi = $request->provinsi;
        $kota_kabupaten = $request->kota_kabupaten;
        $kecamatan = $request->kecamatan;
        $status_perkawinan = $request->status_perkawinan;
        $umur = $request->umur;
        $anak_ke = $request->anak_ke;
        $pendidikan_terakhir = $request->pendidikan_terakhir;
        $jurusan = $request->jurusan;
        $nama_tempat_bekerja = $request->nama_tempat_bekerja;
        $alamat_tempat_bekerja = $request->alamat_tempat_bekerja;
        $penghasilan = $request->penghasilan;
        $pekerjaan_kode = $request->pekerjaan_kode;
        $kewarganegaraan_kode = $request->kewarganegaraan_kode;
        $nama_pasangan = $request->nama_pasangan;
        $nama_ayah = $request->nama_ayah;
        $nomor_rekam_medis_ayah = $request->nomor_rekam_medis_ayah;
        $nama_ibu = $request->nama_ibu;
        $nomor_rekam_medis_ibu = $request->nomor_rekam_medis_ibu;
        $alergi = $request->alergi;

        $foto_identitas = $request->foto_kartu_identitas_pasien;
        $swafoto = $request->foto_swa_pasien;

        $email = $request->email;
        $password = $request->password;

        // cek apakah email sudah dipakai sebelumnya
        $email_pakai = User::where('email', $email)->first();
        if ($email_pakai != null) return ResponseFormatter::error_not_found("Email Sudah Dipakai", null);

        // cek apakah jenis identitas dan nomor identitas sudah dipakai sebelumnya
        $nomor_identitas_pakai = Pasien::where('no_identitas', $nomor_identitas)->first();
        if ($nomor_identitas_pakai != null) return ResponseFormatter::error_not_found("Nomor Identitas Sudah Dipakai", null);

        try {
            // buat data di tb pasien
            try {
                $pasien = new Pasien();
                $pasien->kode = $nomor_rekam_medik;
                $pasien->no_identitas = $nomor_identitas;
                $pasien->jenis_identitas_kode = $jenis_identitas;
                $pasien->nama = $nama_lengkap;
                $pasien->tempat_lahir = $tempat_lahir;
                $pasien->tanggal_lahir = $tanggal_lahir;
                $pasien->kedudukan_keluarga = $kedudukan_keluarga;
                $pasien->golongan_darah = $golongan_darah;
                $pasien->agama_kode = $agama;
                $pasien->suku_kode = $suku;
                $pasien->no_telp = $nomor_telepon;
                $pasien->jkel = $jenis_kelamin;
                $pasien->alamat = $alamat;
                $pasien->provinsi = $provinsi;
                $pasien->kabupaten = $kota_kabupaten;
                $pasien->kecamatan = $kecamatan;
                $pasien->status_perkawinan = $status_perkawinan;
                $pasien->umur = $umur;
                $pasien->anak_ke = $anak_ke;
                $pasien->pendidikan_kode = $pendidikan_terakhir;
                $pasien->jurusan = $jurusan;
                $pasien->nama_tempat_bekerja = $nama_tempat_bekerja;
                $pasien->alamat_tempat_bekerja = $alamat_tempat_bekerja;
                $pasien->penghasilan = $penghasilan;
                $pasien->pekerjaan_kode = $pekerjaan_kode;
                $pasien->kewarganegaraan_kode = $kewarganegaraan_kode;
                $pasien->nama_pasangan = $nama_pasangan;
                $pasien->ayah_nama = $nama_ayah;
                $pasien->no_rekam_medik_ayah = $nomor_rekam_medis_ayah;
                $pasien->ibu_nama = $nama_ibu;
                $pasien->no_rekam_medik_ibu = $nomor_rekam_medis_ibu;
                $pasien->alergi = $alergi;
                $pasien->save();
            } catch (Exception $e) {
                return ResponseFormatter::internal_server_error(
                    'Ada Yang Error Dari Server (pasien)', $e);
            }

            // cari data pasien yang sudah dibuat tadi
            $cari_pasien = Pasien::where('kode', $nomor_rekam_medik)->first();


            // buat data di tb users
            try {
                $akun = new User();
                $akun->name = $nama_lengkap;
                $akun->email = $email;
                $akun->kode = $cari_pasien->kode;
                $akun->password = Hash::make($password);

                $akun->save();
            } catch (Exception $e) {
                return ResponseFormatter::internal_server_error(
                    'Ada Yang Error Dari Server (users)', [$akun, $e]);
            }

            // cari data users yang sudah dibuat tadi
            $cari_akun = User::where('kode', $cari_pasien->kode)->first();

            // buat data di tb detail_akun
            try {
                $detail_akun = new DetailAkun();
                $detail_akun->id_pasien = $cari_pasien->kode;
                $detail_akun->id_akun = $cari_akun->id;
                $detail_akun->save();
            } catch (Exception $e) {
                return ResponseFormatter::internal_server_error(
                    'Ada Yang Error Dari Server (detail_akun)', [$detail_akun, $e]);
            }

            // buat data di tb foto_pasien
            try {
                $foto_pasien = new FotoPasien();
                $path_swa = FotoPasien::$FOTO_SWA_PASIEN;
                $path_kartu_identitas = FotoPasien::$FOTO_KARTU_IDENTITAS_PASIEN;
                $foto_swa_pasien_tb = Foto::base_64_foto($path_swa, $swafoto, $nama_lengkap);
                $foto_kartu_identitas_tb = Foto::base_64_foto($path_kartu_identitas, $foto_identitas, $nama_lengkap);
                $foto_pasien->id_pasien = $cari_pasien->kode;
                $foto_pasien->foto_swa_pasien = $foto_swa_pasien_tb;
                $foto_pasien->foto_kartu_identitas_pasien = $foto_kartu_identitas_tb;
                $foto_pasien->save();
            } catch (Exception $e) {
                return ResponseFormatter::internal_server_error(
                    'Ada Yang Error Dari Server (foto_pasien)', $e);
            }

            //buat data di tb penanggung
            try {
                $list_penanggung = array();
                foreach ($request->daftar_penanggung as $penanggungs) {
                    $penanggung = new Penanggung();
                    $penanggung->nama_penanggung = $penanggungs['nama_penanggung'];
                    $penanggung->nomor_kartu_penanggung = $penanggungs['nomor_kartu_penanggung'];
                    $penanggung->pasien_id = $cari_pasien->kode;

                    $path = Penanggung::$FOTO_KARTU_PENANGGUNG;
                    $key = $penanggungs['foto_kartu_penanggung'];

                    if ($penanggungs['foto_kartu_penanggung']) {
                        $file = Foto::base_64_foto($path, $key, $nama_lengkap);
                        $penanggung->foto_kartu_penanggung = $file;
                    }
                    $penanggung->save();
                    $list_penanggung[] = $penanggung;
                }
                
                
                //return ResponseFormatter::success_ok('Berhasil Membuat Penanggung', $list_penanggung);
            } catch (Exception $e) {
                return ResponseFormatter::internal_server_error(
                    'Ada Yang Error Dari Server(penangggung)',[$list_penanggung,$e]);
            }

            $response = [];
            $response["nomor_identitas"] = $nomor_identitas;
            $response["jenis_identitas"] = $jenis_identitas;
            $response["nama_lengkap"] = $nama_lengkap;
            $response["tempat_lahir"] = $tempat_lahir;
            $response["tanggal_lahir"] = $tanggal_lahir;
            $response["kedudukan_keluarga"] = $kedudukan_keluarga;
            $response["golongan_darah"] = $golongan_darah;
            $response["agama"] = $agama;
            $response["suku"] = $suku;
            $response["nomor_telepon"] = $nomor_telepon;
            $response["jenis_kelamin"] = $jenis_kelamin;
            $response["alamat"] = $alamat;
            $response["suku"] = $suku;
            $response["provinsi"] = $provinsi;
            $response["kota_kabupaten"] = $kota_kabupaten;
            $response["kecamatan"] = $kecamatan;
            $response["status_perkawinan"] = $status_perkawinan;
            $response["umur"] = $umur;
            $response["anak_ke"] = $anak_ke;
            $response["pendidikan_terakhir"] = $pendidikan_terakhir;
            $response["jurusan"] = $jurusan;
            $response["nama_tempat_bekerja"] = $nama_tempat_bekerja;
            $response["alamat_tempat_bekerja"] = $alamat_tempat_bekerja;
            $response["penghasilan"] = $penghasilan;
            $response["pekerjaan_kode"] = $pekerjaan_kode;
            $response["kewarganegaraan_kode"] = $kewarganegaraan_kode;
            $response["nama_pasangan"] = $nama_pasangan;
            $response["nama_ayah"] = $nama_ayah;
            $response["nomor_rekam_medis_ayah"] = $nomor_rekam_medis_ayah;
            $response["nama_ibu"] = $nama_ibu;
            $response["nomor_rekam_medis_ibu"] = $nomor_rekam_medis_ibu;
            $response["alergi"] = $alergi;
            
            $response["email"] = $email;
            $response["password"] = $password;

            $response["foto_swa_pasien"] = $foto_swa_pasien_tb;
            $response["foto_kartu_identitas_pasien"] = $foto_kartu_identitas_tb;
            
            $list_penanggung1 = array();
            foreach ($request->daftar_penanggung as $penanggungs) {
                $penanggung = new Penanggung();
                $penanggung->nama_penanggung = $penanggungs['nama_penanggung'];
                $penanggung->nomor_kartu_penanggung = $penanggungs['nomor_kartu_penanggung'];

                $path = Penanggung::$FOTO_KARTU_PENANGGUNG;
                $key = $penanggungs['foto_kartu_penanggung'];

                if ($penanggungs['foto_kartu_penanggung']) {
                    $file = Foto::base_64_foto_pindah_aja($path, $key, $nama_lengkap);
                    $penanggung->foto_kartu_penanggung = $file;
                }
                $list_penanggung1[] = $penanggung;
            }

            $response["daftar_penanggung"] = $list_penanggung1;

            return ResponseFormatter::success_ok("Berhasil Mendaftar", $response);
        } catch (Exception $e) {
            return ResponseFormatter::internal_server_error('Ada Yang Error Dari Server(all)', $e);
        }
    }

    public function pendaftaranPasienLama(Request $request)
    {
        try{
            // get value text
            $no_rekam_medik = $request->kode;
            $tgl_lahir = $request->tanggal_lahir;
            $jenis_identitas = $request->jenis_identitas;
            $nomor_identitas = $request->nomor_identitas;
            $foto_swa_pasien = $request->foto_swa_pasien;
            $foto_kartu_identitas_pasien = $request->foto_kartu_identitas_pasien;
            $email = $request->email;
            $password = $request->password;
            $ulang_password = $request->ulang_password;

            // cek ke db pasien
            $pasien = Pasien::where('kode', $no_rekam_medik)->first();

            // cek apakah data pasien null atau tidak
            if ($pasien == null) {
                return ResponseFormatter::error_not_found("Data Pasien Tidak Ada", null);
            } else {
                //cek ke db user apakah pasien sudah mendaftar sebelumnya
                $user = User::where('kode', $pasien->kode)->first();
                if ($user != null) {
                    //jika null atau tidak ada data maka lanjut ke step selanjutnya
                    if ($pasien->kode == $user->kode) {
                        //jika sudah ada data maka berhenti disni
                        ResponseFormatter::error_not_found("Akun Sudah Terdaftar", null);
                    }
                }
            }

            // logika seluruh validasi tidak termasuk angka/text/strinf dll
            if ($pasien == null) return ResponseFormatter::error_not_found("Nomor Rekam Medik Tidak Terdaftar", null);

            if ($pasien->tanggal_lahir != $tgl_lahir) return ResponseFormatter::error_not_found("Tanggal Lahir Tidak Sesuai", null);

            if ($pasien->jenis_identitas_kode != $jenis_identitas) return ResponseFormatter::error_not_found("Jenis Identitas Tidak Sesuai", null);

            if ($pasien->no_identitas != $nomor_identitas) return ResponseFormatter::error_not_found("Nomor Identitas Tidak Benar", null);

            if ($password != $ulang_password) return ResponseFormatter::error_not_found("Password Tidak Sama", null);

            // path 
            $path_kartu_identitas = FotoPasien::$FOTO_KARTU_IDENTITAS_PASIEN;
            $path_swa = FotoPasien::$FOTO_SWA_PASIEN;

            //base64 to image
            $nama_kartu_identitas_foto = Foto::base_64_foto($path_kartu_identitas, $foto_kartu_identitas_pasien, $pasien->nama);
            $nama_swafoto = Foto::base_64_foto($path_swa, $foto_swa_pasien, $pasien->nama);

            // buat data di table users
            $create_users = new User();
            $create_users->name = $pasien->nama;
            $create_users->email = $email;
            $create_users->password = Hash::make($password);
            $create_users->kode = $pasien->kode;
            $create_users->save();

            // cari data users yang sudah dibuat tadi
            $cari_akun = User::where('kode', $pasien->kode)->first();

            // buat data di table penanggung umum
            $penanggung = new Penanggung();
            $penanggung->nama_penanggung = "1";
            $penanggung->nomor_kartu_penanggung = null;
            $penanggung->pasien_id = $pasien->kode;

            // buat data di table foto pasien
            $create_foto_pasien = new FotoPasien();
            $create_foto_pasien->id_pasien = $pasien->kode;
            $create_foto_pasien->foto_swa_pasien = $nama_swafoto;
            $create_foto_pasien->foto_kartu_identitas_pasien = $nama_kartu_identitas_foto;
            
            // buat data di tb detail_akun
            try {
                $detail_akun = new DetailAkun();
                $detail_akun->id_pasien = $pasien->kode;
                $detail_akun->id_akun = $cari_akun->id;
                $detail_akun->is_lama = "1";
            } catch (Exception $e) {
                return ResponseFormatter::internal_server_error(
                    'Ada Yang Error Dari Server (detail_akun)', [$detail_akun, $e]);
            }

            //$c = array($create_users->first(), $create_foto_pasien->first());
            // $data[] = array($create_users->first() + $create_foto_pasien->first());

            try {
                //jika berhasil
                $create_foto_pasien->save();
                $detail_akun->save();
                $penanggung->save();
                $response = [];
                $response["kode"] = $no_rekam_medik;
                $response["email"] = $email;
                $response["password"] = $password;
                $response["ulang_password"] = $ulang_password;
                $response["foto_swa_pasien"] = $nama_swafoto;
                $response["foto_kartu_identitas_pasien"] = $nama_kartu_identitas_foto;
                $response["tanggal_lahir"] = $tgl_lahir;
                $response["jenis_identitas"] = $jenis_identitas;
                $response["no_identitas"] = $nomor_identitas;
                return ResponseFormatter::success_ok('Berhasil Mendaftar Akun', $response);
            } catch (Exception $e) {
                //jika gagal
                return ResponseFormatter::internal_server_error('Ada Sesuatu Yang salah', $e);
            }
        }catch(Exception $e){
            return ResponseFormatter::internal_server_error('Ada Sesuatu Yang salah', $e);
        }
    }

    public function dapatkanKodeOtpPendaftaranAKun(Request $request)
    {
        try{
            // input email
            $email = $request->email;

            // angka random untuk otp diubah menjadi hash
            $pass= rand(1000, 9999);
            $kode_otp = Hash::make($pass);

            // waktu expired untuk otp
            //$date_now = time();
            $date_expired = time()+1800;
            // $date = $date_now - $date_expired;

            // email mengcek db
            // $user = User::where('email', $email)->first();

            // if($user === null){
            //     return ResponseFormatter::error_not_found(
            //         'Email Tidak Terdaftar',
            //         null
            //     );
            // }

            // cek email di table otp
            $otp = Otp::whereEmail($email)->first();
            // echo $otp;
            // die();
            
            //$getEmail =  $user->email;
            $response = [];
            $response['email'] = $email;
            $response['kode_otp'] = $pass;

            if($user =! $otp){
                $create_otp = new Otp();
                $create_otp->email = $email;
                $create_otp->kode_otp = $kode_otp;
                $create_otp->expired_time = $date_expired;
                $create_otp->save();

                $details = [
                    'title' => 'MIRAI Pasien OTP',
                    'body' => 'OTP Untuk Pasien Baru',
                    'otp' => 'Ini Adalah Kode Anda',
                    'hash_otp' => $pass
                ];

                Mail::to($email)->send(new MyTestMail($details));

                return ResponseFormatter::success_ok(
                    "Berhasil Mengirim OTP", 
                    $response
                );
            }else if ($otp->email == $email){
                $update_otp = Otp::find($otp->id);
                $update_otp->email = $email;
                $update_otp->kode_otp = $kode_otp;
                $update_otp->expired_time = $date_expired;
                $update_otp->save();

                $details = [
                    'title' => 'MIRAI Pasien OTP',
                    'body' => 'OTP Untuk Pasien Baru',
                    'otp' => 'Ini Adalah Kode Anda',
                    'hash_otp' => $pass
                ];
        
                Mail::to($email)->send(new MyTestMail($details));

                return ResponseFormatter::success_ok(
                    'Berhasil Update OTP',
                    $response
                );
            }else{
                return ResponseFormatter::internal_server_error(
                    'Kesalahan Pada Server',
                    $user
                );
            }
        }catch(Exception $e){   
            return ResponseFormatter::internal_server_error(
                'Kesalahan Pada Server',
                $e
            );
        }
    }

    public function konfirmasiKodeOtpPendaftaranAkun(Request $request)
    {
        try{
            $email = $request->email;
            $kode_otp = $request->kode_otp;

            // email mengcek db
            $otp = Otp::where('email', $email)->first();

            // cek apakah ada data? jika tidak return
            if($otp === null){
                return ResponseFormatter::error_not_found(
                    'Email Tidak Terdaftar',
                    null
                );
            }

            $otpHash =  $otp->kode_otp;
            $expired_time = $otp->expired_time;
            $getOtp = Hash::check($kode_otp, $otpHash);

            $response = [];
            $response['email'] = $email;
            $response['kode_otp'] = intval($kode_otp);

            if($kode_otp == $getOtp){
                if($expired_time >= time()){
                    $otp_delete = Otp::find($otp->id);
                    $otp_delete->delete();
                    return ResponseFormatter::success_ok(
                        'Berhasil Validasi OTP',
                        $response
                    );
                }else{
                    return ResponseFormatter::error_not_found(
                        'Kode OTP Sudah Expired',
                        null
                    );
                }
            }else{
                return ResponseFormatter::error_not_found(
                    'Kode OTP Salah',
                    null
                );
            }
        }catch (Exception $e){
            return ResponseFormatter::internal_server_error(
                'Kesalahan Pada Server', $e
            );
        }
    }
}
