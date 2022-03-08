<?php

namespace App\Http\Controllers\V1;

use App\Dummy\DataDummy;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\V1\RiwayatPoliklinik;
use Exception;

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
                $response[] = $array_riwayat;
            }

            return ResponseFormatter::success_ok('Berhasil Mendapatkan Data', $response);
        }catch (Exception $e){
            return ResponseFormatter::internal_server_error('Error Dari Server', $e);
        }
    }
}
