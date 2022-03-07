<?php

namespace App\Http\Controllers\V1;

use App\Dummy\DataDummy;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\V1\Antrian;
use App\Models\V1\DetailAkun;
use App\Models\V1\Pasien;
use App\Models\V1\PendaftaranPoliklinik;
use App\Models\V1\Poli;
use Exception;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isEmpty;

class PendaftaranPoliklinikController extends Controller
{
    public function getNomorRM(Request $request)
    {
        $email = $request->input('email');
        $akun = User::where('email', $email)->first();
        $id_akun = $akun->id;
        $detail_akun = DetailAkun::where('id_akun', $id_akun)->get();
        $a = User::find($id_akun);

        $response = [];
        $a['details'] = $a->details;

        $list_pasien = [];
        foreach($a['details'] as $bc){
            $pasien = Pasien::where('kode', sprintf("%08s", strval($bc->id_pasien)))->orderBy('kode', 'desc')->get();
            foreach($pasien as $p){
                $list_pasien['nomor_rekam_medis'] = sprintf("%08s", strval($p->kode));
                $list_pasien['nama'] = $p->nama;
                $list_pasien['nama_nomor'] = (sprintf("%08s", strval($p->kode))."-". $p->nama);
                $response[] = $list_pasien;
            }
        }
        return ResponseFormatter::success_ok("Data Berhasil Diteukan", $response);
    }

    public function getHariBerobat()
    {
        $hari = DataDummy::dummyHari();
        return ResponseFormatter::success_ok("Berhasil Mendapatkan Data", $hari);
    }

    public function getDebitur()
    {
        $debitur = DataDummy::dummyDebitur();
        return ResponseFormatter::success_ok("Berhasil Mendapatkan Data", $debitur);
    }

    public function daftarPoliklinik(Request $request)
    {
        
        try{
            $nama_pasien = $request->nama_pasien;
            $nomor_rekam_medis = $request->nomor_rekam_medis;
            $kunjungan = $request->kunjungan;
            $nomor_debitur = $request->nomor_debitur;
            $id_poliklinik = $request->id_poliklinik;
            $id_user = $request->id_user;

            $a = Antrian::where('id_poli', $id_poliklinik)->orderBy('id', 'DESC')->first();
            $antrian = $a->nomor_antrian+1;
            $antrian_real = sprintf("%03s", strval($antrian));

            $response = [];
            $response['nama_pasien'] = $nama_pasien;
            $response['nomor_rekam_medis'] = $nomor_rekam_medis;
            $response['kunjungan'] = $kunjungan;
            $response['nomor_debitur'] = $nomor_debitur;
            $response['id_poliklinik'] = $id_poliklinik;
            $response['id_user'] = $id_user;

            $daftar = new PendaftaranPoliklinik();
            $daftar->nama_pasien = $nama_pasien;
            $daftar->nomor_rekam_medis = $nomor_rekam_medis;
            $daftar->kunjungan = $kunjungan;
            $daftar->nomor_debitur = $nomor_debitur;
            $daftar->id_poliklinik = $id_poliklinik;
            $daftar->id_user = $id_user;
            $daftar->status_pendaftaran = "0";
            $daftar->nomor_antrian = $antrian_real;
            $daftar->save();
            
            return ResponseFormatter::success_ok("Berhasil Mendaftar", $response);
        }catch (Exception $e){
            return ResponseFormatter::internal_server_error("Ada Yang Salah Dari Server", $e);
        }
    }

    public function getPendaftaranPoliklinik(Request $request)
    {
        $id_user = $request->input('id_user');
        $validasi_pendaftaran = PendaftaranPoliklinik::where('id_user', $id_user)->first();
        if($validasi_pendaftaran == null) return "Data Kosong";

        $pendaftaran = PendaftaranPoliklinik::where('id_user', $id_user)->get();
        $antrian_a = Antrian::where('panggil', "1")->where('id_poli', "1")->orderBy('id', 'asc')->first();
        $list_pendaftaran = [];
        $response = [];
        foreach($pendaftaran as $p){
            if($p->status_pendaftaran == 0){
                $status_pendaftaran_detail = "Booking";
            }else if($p->status_pendaftaran == 1){
                $status_pendaftaran_detail = "Aktif";
            }
            $poli = DataDummy::dummyPilihPoli($p->id_poliklinik);
            $nama_poli = $poli[0]['nama'];
            if($p->nomor_debitur == null){
                $nomor_debitur = "Umum";
            }else{
                $nomor_debitur = $p->nomor_debitur;
            }
            if($antrian_a == null){
                $detail_antrian = "000";
            }else{
                $detail_antrian = $antrian_a->nomor_antrian;
            }
            $list_pendaftaran['id'] = $p->id;
            $list_pendaftaran['nama_pasien'] = $p->nama_pasien;
            $list_pendaftaran['nomor_rekam_medis'] = sprintf("%08s", strval($p->nomor_rekam_medis));
            $list_pendaftaran['kunjungan'] = $p->kunjungan;
            $list_pendaftaran['nomor_debitur'] = $nomor_debitur;
            $list_pendaftaran['nama_poliklinik'] = ("Poli ". $nama_poli);
            $list_pendaftaran['status_pendaftaran'] = $status_pendaftaran_detail;
            $list_pendaftaran['nomor_antrian'] = $p->nomor_antrian;
            $list_pendaftaran['nomor_antrian_berjalan'] = $detail_antrian;
            $response[] = $list_pendaftaran;
        }

        return ResponseFormatter::success_ok("Berhasil Mendapat Data", $response);
    }
}
