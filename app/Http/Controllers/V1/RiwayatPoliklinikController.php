<?php

namespace App\Http\Controllers\V1;

use Exception;
use Carbon\Carbon;
use App\Dummy\DataDummy;
use Illuminate\Http\Request;
use App\Models\V1\Penanggung;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\V1\RiwayatPoliklinik;
use App\Models\V1\RiwayatResumeMedis;
use App\Models\V1\RiwayatHasilPenunjang;

class RiwayatPoliklinikController extends Controller
{
    public function getRiwayatPoliklinik(Request $request)
    {
        try{
            $nomor_rm = $request->input('nomor_rekam_medis');

            // cek kosong atau tidak datanya
            $cek = RiwayatPoliklinik::where('nomor_rekam_medis', $nomor_rm)->first();
            if($cek == null) return ResponseFormatter::error_not_found("Data Tidak Ditemukan", null);

            // jika ada
            $riwayat = RiwayatPoliklinik::where('nomor_rekam_medis', $nomor_rm)->get();
            $array_riwayat = [];
            $array_riwayat_medis = [];
            $array_hasil_penunjang = [];

            //response
            $response = [];

            // ambil data
            foreach($riwayat as $r){
                $array_rm = [];
                $array_hp = [];
                //cari nama penanggung
                $cari_penanggung = Penanggung::where('pasien_id', (int)$r->nomor_rekam_medis)->where('nomor_kartu_penanggung', $r->nomor_debitur)->first();
                if($cari_penanggung == null){
                    $nama_penanggung = "UMUM";
                }else{
                    $kode = $cari_penanggung->nama_penanggung;
                    if($kode == "1") {
                        $nama_penanggung = "BPJS";
                    }elseif($kode == "2"){
                        $nama_penanggung = "KIS";
                    }elseif($kode == "3"){
                        $nama_penanggung = "JAMKESDA";
                    }
                }

                $my_date = $r->tanggal_daftar;
                $date = Carbon::createFromFormat('Y-m-d', $my_date)->locale('id')->isoFormat('dddd, D MMMM Y ');
                $poli = DataDummy::dummyPilihPoli($r->id_poliklinik);
                $nama_poli = $poli[0]['nama'];
                
                // ambil data riwayat resume medis
                $resume_medis = RiwayatResumeMedis::where('nomor_daftar_poliklinik', $r->nomor_daftar)->get();
                if(!count($resume_medis) == 0){
                    foreach($resume_medis as $rm){
                        $array_riwayat_medis['resume_medis_detail'] = $rm->resume_medis_detail;
                        $array_rm[] = $array_riwayat_medis;
                    }
                }
                

                // ambil data riwayat hasil penunjang
                $hasil_penunjang = RiwayatHasilPenunjang::where('nomor_daftar_poliklinik', $r->nomor_daftar)->get();
                if(!count($hasil_penunjang) == 0){
                    foreach($hasil_penunjang as $hp){
                        $array_hasil_penunjang['hasil_penunjang_detail'] = $hp->hasil_penunjang_detail;
                        $array_hp[] = $array_hasil_penunjang;
                    }
                }

                // respon yang ditampilkan
                $array_riwayat['nomor_daftar'] = $r->nomor_daftar;
                $array_riwayat['nama_pasien'] = $r->nama_pasien;
                $array_riwayat['nomor_rekam_medis'] = $r->nomor_rekam_medis;
                $array_riwayat['nama_penanggung'] = $nama_penanggung;
                $array_riwayat['nomor_penanggung'] = $r->nomor_debitur;
                $array_riwayat['nama_poliklinik'] = $nama_poli;
                $array_riwayat['tanggal_daftar'] = $date;
                $array_riwayat['resume_medis'] = $array_rm;
                $array_riwayat['hasil_penunjang'] = $array_hp;
                $response[] = $array_riwayat;
            }

            return ResponseFormatter::success_ok('Berhasil Mendapatkan Data', $response);
        }catch (Exception $e){
            return ResponseFormatter::internal_server_error('Error Dari Server', $e);
        }
    }

    public function oldgetRiwayatPoliklinik(Request $request)
    {
        try{
            $nomor_rm = $request->input('nomor_rekam_medis');

            // cek kosong atau tidak datanya
            $cek = RiwayatPoliklinik::where('nomor_rekam_medis', $nomor_rm)->first();
            if($cek == null) return ResponseFormatter::error_not_found("Data Tidak Ditemukan", null);

            // jika ada
            $riwayat = RiwayatPoliklinik::where('nomor_rekam_medis', $nomor_rm)->get();
            $array_riwayat = [];
            $array_riwayat_medis = [];
            $array_hasil_penunjang = [];
            $array = [];

            //response
            $response = [];

            // ambil data
            foreach($riwayat as $r){
                $poli = DataDummy::dummyPilihPoli($r->id_poliklinik);
                $nama_poli = $poli[0]['nama'];
                $array_riwayat['nomor_daftar'] = $r->nomor_daftar;
                $array_riwayat['nama_pasien'] = $r->nama_pasien;
                $array_riwayat['nomor_rekam_medis'] = $r->nomor_rekam_medis;
                $array_riwayat['nama_poliklinik'] = $nama_poli;
                $array_riwayat['tanggal_daftar'] = $r->tanggal_daftar;
                $array_riwayat['resume_medis'] = $r->resume_medis;
                $array_riwayat['hasil_penunjang'] = $r->hasil_penunjang;
                
                // ambil data riwayat resume medis
                $resume_medis = RiwayatResumeMedis::where('nomor_daftar_poliklinik', $r->nomor_daftar)->get();
                foreach($resume_medis as $rm){
                    $array_riwayat_medis['resume_medis_detail'] = $rm->resume_medis_detail;
                    
                }

                // ambil data riwayat hasil penunjang
                $hasil_penunjang = RiwayatHasilPenunjang::where('nomor_daftar_poliklinik', $r->nomor_daftar)->get();
                foreach($hasil_penunjang as $hp){
                    $array_hasil_penunjang['hasil_penunjang_detail'] = $hp->hasil_penunjang_detail;
                    $response["dada"] = $array_hasil_penunjang;
                }
                $response['nomor_daftar'] = $r->nomor_daftar;
            }

            return ResponseFormatter::success_ok('Berhasil Mendapatkan Data', $response);
        }catch (Exception $e){
            return ResponseFormatter::internal_server_error('Error Dari Server', $e);
        }
    }
}
