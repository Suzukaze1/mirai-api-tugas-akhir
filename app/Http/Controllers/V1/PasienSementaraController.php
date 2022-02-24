<?php

namespace App\Http\Controllers\V1;

use Exception;
use App\Models\User;
use App\Helpers\Foto;
use App\Models\V1\Pasien;
use Illuminate\Http\Request;
use App\Models\V1\DetailAkun;
use App\Models\V1\FotoPasien;
use App\Models\V1\Penanggung;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\V1\PasienSementara;
use Illuminate\Support\Facades\Hash;

class PasienSementaraController extends Controller
{
    public function pendaftaranPasienBaruKeTabelSementara(Request $request)
    {
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

        // cek apakah jenis identitas sementara dan nomor identitas sementara sudah dipakai sebelumnya
        $nomor_identitas_pakai_sementara = PasienSementara::where('no_identitas', $nomor_identitas)->first();
        if ($nomor_identitas_pakai_sementara != null) return ResponseFormatter::error_not_found("Nomor Identitas Sudah Dipakai", null);

        try {
            // buat data di tb pasien_temp
            try {
                $pasien = new PasienSementara();
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
                $pasien->status_validasi = "0";
                $pasien->save();
            } catch (Exception $e) {
                return ResponseFormatter::internal_server_error(
                    'Ada Yang Error Dari Server (pasien)', $e);
            }

            // cari data pasien yang sudah dibuat tadi
            $cari_pasien = PasienSementara::where('id', $pasien->id)->first();


            // buat data di tb users
            try {
                $akun = new User();
                $akun->name = $nama_lengkap;
                $akun->email = $email;
                $akun->id_pasien_temp = $cari_pasien->id;
                $akun->password = Hash::make($password);

                $akun->save();
            } catch (Exception $e) {
                return ResponseFormatter::internal_server_error(
                    'Ada Yang Error Dari Server (users)', [$akun, $e]);
            }

            // cari data users yang sudah dibuat tadi
            $cari_akun = User::where('id_pasien_temp', $cari_pasien->id)->first();

            // buat data di tb detail_akun
            try {
                $detail_akun = new DetailAkun();
                $detail_akun->id_pasien_temp = $cari_pasien->id;
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
                $foto_pasien->id_pasien_temp = $cari_pasien->id;
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
                    $penanggung->id_pasien_temp = $cari_pasien->id;

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
}
