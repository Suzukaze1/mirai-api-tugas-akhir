<?php

namespace App\Http\Controllers\V1;

use Exception;
use App\Models\User;
use App\Models\V1\Suku;
use App\Models\V1\Agama;
use App\Models\V1\Pasien;
use App\Models\V1\Jurusan;
use App\Models\V1\Provinsi;
use App\Models\V1\Kecamatan;
use Illuminate\Http\Request;
use App\Models\V1\DetailAkun;
use App\Models\V1\FotoPasien;
use App\Models\V1\Penanggung;
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
use App\Models\V1\PendidikanTerakhir;

class AnggotaPasienController extends Controller
{
    public function getAnggotaIndukPasien(Request $request)
    {
        try{
            $email = $request->input('email');

            $user = User::where('email', $email)->first();

            if($user == null) return ResponseFormatter::error_not_found("data tidak ditemukan", null);

            $detail_akun = DetailAkun::where('id_akun', $user->id)->orderBy('id', 'asc')->get();

            unset($detail_akun[0]) ;

            $data_anggota = [];
            $response = [];
            foreach($detail_akun as $d){
                if($d->id_pasien == null){
                    $data_pasien_sem = PasienSementara::where('id', $d->id_pasien_temp)->first();
                    $data_anggota['nomor_rekam_medis'] = "-";
                    $data_anggota['nama_anggota'] = $data_pasien_sem->nama;
                    $data_anggota['id_status_validasi'] = "0";
                    $data_anggota['nama_status'] = "Sedang di Validasi";
                    $data_anggota['id_pasien_sementara'] = $data_pasien_sem->id;
                    $response[] = $data_anggota;
                }else if($d->id_pasien_temp == null){
                    $data_pasien = Pasien::where('kode', sprintf("%08s", strval($d->id_pasien)))->first();
                    $data_anggota['nomor_rekam_medis'] = sprintf("%08s", strval($data_pasien->kode));
                    $data_anggota['nama_anggota'] = $data_pasien->nama;
                    $data_anggota['id_status_validasi'] = "1";
                    $data_anggota['nama_status'] = "Berhasil di Validasi";
                    $response[] = $data_anggota;
                }
            }
            return ResponseFormatter::success_ok("Berhasil Mendapatkan Data", $response);
        }catch (Exception $e){
            return ResponseFormatter::internal_server_error("Kesalahan Dari Server", $e);
        }
    }

    public function getDetailAnggotaIndukPasien(Request $request)
    {
        $nomor_rekam_medis = $request->input('nomor_rekam_medis');
        $id_status_validasi = $request->input('id_status_validasi');

        $response = [];

        if($id_status_validasi == 1){
            $detail = Pasien::where('kode', $nomor_rekam_medis)->first();

            if($detail == null) return ResponseFormatter::error_not_found("Data Tidak Ditemukan", null);

            //data master
            $agama = Agama::where('kode', $detail->agama_kode)->first();
            $pendidikan_terakhir = PendidikanTerakhir::where('kode', $detail->pendidikan_kode)->first();
            $kewarganegaraan_kode = Kewarganegaraan::where('kode', $detail->kewarganegaraan_kode)->first();
            $jenis_identitas_kode = jenis_identitas::where('kode', $detail->jenis_identitas_kode)->first();
            $suku_kode = Suku::where('kode', $detail->suku_kode)->first();
            $jenis_kelamin = JenisKelamin::where('kode', $detail->jkel)->first();
            $status_perkawinan = StatusMenikah::where('kode', $detail->status_perkawinan)->first();
            $kedudukan_keluarga = KedudukanKeluarga::where('kode', $detail->kedudukan_keluarga)->first();
            $golongan_darah = GolonganDarah::where('kode', $detail->golongan_darah)->first();
            $provinsi = Provinsi::where('kode', $detail->provinsi)->first();
            $kabupaten = KotaKabupaten::where('kode', $detail->kabupaten)->first();
            $kecamatan = Kecamatan::where('kode', $detail->kecamatan)->first();
            $jurusan = Jurusan::where('kode', $detail->jurusan)->first();
            $penghasilan = Penghasilan::where('kode', $detail->penghasilan)->first();
            $penanggung = Penanggung::where('id_pasien_temp', $detail->id)->first();
            $foto_pasien = FotoPasien::where('id_pasien_temp', $detail->id)->first();
            $akun = User::where('id_pasien_temp', $detail->id)->first();

            $nama_agama = $agama->agama;
            $pend_terakhir = $pendidikan_terakhir->nama;
            $kewarganegaraan = $kewarganegaraan_kode->nama;

            return $kewarganegaraan;

            $response['nomor_rekam_medis'] = $detail->kode; 
            $response['nama_pasien'] = $detail->nama;
        }else if($id_status_validasi == 2){
            $detail_sem = PasienSementara::where('id', $nomor_rekam_medis)->first();

            if($detail_sem == null) return ResponseFormatter::error_not_found("Data Tidak Ditemukan", null);

            $response['nomor_rekam_medis'] = $detail_sem->id; 
            $response['nama_pasien'] = $detail_sem->nama;
        }

        return $response;
    }
}
