<?php

namespace App\Http\Controllers\V1;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\V1\Poli;
use App\Dummy\DataDummy;
use App\Models\V1\Pasien;
use App\Models\V1\Antrian;
use Illuminate\Http\Request;
use App\Models\V1\DetailAkun;
use App\Models\V1\Penanggung;
use App\Models\V1\NamaPenanggung;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\V1\RiwayatPoliklinik;

use function PHPUnit\Framework\isEmpty;
use App\Models\V1\PendaftaranPoliklinik;

class PendaftaranPoliklinikController extends Controller
{
    public function getNomorRM(Request $request)
    {
        $email = $request->input('email');
        $akun = User::where('email', $email)->first();
        if($akun == null) return ResponseFormatter::error_not_found("Email Data Tidak Ditemukan", null);
        $id_akun = $akun->id;
        $detail_akun = DetailAkun::where('id_akun', $id_akun)->get();
        $a = User::find($id_akun);
        if($a == null) return ResponseFormatter::error_not_found("Data Tidak Ditemukan", null);

        $response = [];
        $a['details'] = $a->details;

        $list_pasien = [];
        foreach($a['details'] as $bc){
            $pasien = Pasien::where('kode', sprintf("%08s", strval($bc->id_pasien)))->orderBy('kode', 'desc')->get();
            foreach($pasien as $p){
                $list_pasien['nomor_rekam_medis'] = sprintf("%08s", strval($p->kode));
                $list_pasien['nama'] = $p->nama;
                $list_pasien['nama_nomor'] = (sprintf("%08s", strval($p->kode))." - ". $p->nama);
                $response[] = $list_pasien;
            }
        }
        return ResponseFormatter::success_ok("Data Berhasil Ditemukan", $response);
    }

    public function getHariBerobat()
    {
        $hari = DataDummy::dummyHari();
        return ResponseFormatter::success_ok("Berhasil Mendapatkan Data", $hari);
    }

    public function getDebitur(Request $request)
    {
        try{
            $list_debitur = [];
            $response = [];
            // $list_debitur['jenis_debitur'] = "4";
            
            $nomor_rm = $request->nomor_rekam_medis;
            $no_rm = (int) $nomor_rm;

            $list_debitur['id_penanggung'] = 4;
            $list_debitur['nama_penanggung'] = "Umum";
            $list_debitur['nomor_penanggung'] = null;
            $list_debitur['label_penanggung'] = "Umum";
            $response[]= $list_debitur;

            $get_debitur_pasien = Penanggung::where('pasien_id', $no_rm)->get();

            foreach($get_debitur_pasien as $deb)
            {
                $nam_pen = NamaPenanggung::where('kode', $deb->nama_penanggung)->first();
                $nam_deb = $nam_pen->nama;
                $nama_debitur = $nam_deb." - ".$deb->nomor_kartu_penanggung;
                $list_debitur['id_penanggung'] = (int)$deb->nama_penanggung;
                $list_debitur['nama_penanggung'] = $nam_deb;
                $list_debitur['nomor_penanggung'] = $deb->nomor_kartu_penanggung;
                $list_debitur['label_penanggung'] = $nama_debitur;
                $response[] = $list_debitur;
            }

            return ResponseFormatter::success_ok("Berhasil Mendapatkan Data", $response);
        }catch (Exception $e){
            return ResponseFormatter::internal_server_error("Ada Yang Salah Dari Server, $e");
        }
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
        try{
            $id_user = $request->input('id_user');
            $validasi_pendaftaran = PendaftaranPoliklinik::where('id_user', $id_user)->first();
            if($validasi_pendaftaran == null) return "Data Kosong";

            $pendaftaran = PendaftaranPoliklinik::where('id_user', $id_user)->get();
            $antrian_a = Antrian::where('panggil', "1")->where('id_poli', "1")->orderBy('id', 'asc')->first();
            $list_pendaftaran = [];
            $response = [];
            foreach($pendaftaran as $p){
                if($p->status_pendaftaran == "0"){
                    $status_pendaftaran_detail = "Booking";
                }else if($p->status_pendaftaran == "1"){
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
                if($p->status_pendaftaran == "1"){
                    $list_pendaftaran['id'] = $p->id;
                    $list_pendaftaran['nama_pasien'] = $p->nama_pasien;
                    $list_pendaftaran['nomor_rekam_medis'] = sprintf("%08s", strval($p->nomor_rekam_medis));
                    $list_pendaftaran['kunjungan'] = $p->kunjungan;
                    $list_pendaftaran['nomor_debitur'] = $nomor_debitur;
                    $list_pendaftaran['nama_poliklinik'] = ("Poli ". $nama_poli);
                    $list_pendaftaran['status_pendaftaran'] = $status_pendaftaran_detail;
                    $list_pendaftaran['nomor_antrian'] = $p->nomor_antrian;
                    $list_pendaftaran['nomor_antrian_berjalan'] = $detail_antrian;
                }elseif ($p->status_pendaftaran == "0"){
                    $list_pendaftaran['id'] = $p->id;
                    $list_pendaftaran['nama_pasien'] = $p->nama_pasien;
                    $list_pendaftaran['nomor_rekam_medis'] = sprintf("%08s", strval($p->nomor_rekam_medis));
                    $list_pendaftaran['kunjungan'] = $p->kunjungan;
                    $list_pendaftaran['nomor_debitur'] = $nomor_debitur;
                    $list_pendaftaran['nama_poliklinik'] = ("Poli ". $nama_poli);
                    $list_pendaftaran['status_pendaftaran'] = $status_pendaftaran_detail;
                    $list_pendaftaran['nomor_antrian'] = $p->nomor_antrian;
                }
                $response[] = $list_pendaftaran;
            }

            return ResponseFormatter::success_ok("Berhasil Mendapat Data", $response);
        }catch(Exception $e){
            return ResponseFormatter::internal_server_error("Ada Yang Salah Dari Server", $e);
        }
        
    }

    public function ubahStatusPendaftaran(Request $request)
    {
        try{
            $data = "Aktif";
            $id_pendaftaran = $request->id_pendaftaran;

            $ubah_status = PendaftaranPoliklinik::find($id_pendaftaran);
            $ubah_status->status_pendaftaran = "1";
            $ubah_status->save();

            return ResponseFormatter::success_ok("Berhasil Mengubah Status", $data);
        }catch (Exception $e){
            return ResponseFormatter::internal_server_error("Ada Yang Salah Dari Server", $e);
        }
        
    }

    public function selesaiPendaftaran(Request $request)
    {
        try{
            $id_pendaftaran = $request->id_pendaftaran;

            $cari_pendaftaran = PendaftaranPoliklinik::where('id', $id_pendaftaran)->first();

            // untuk pembuatan nomor daftar
            $id_user = $cari_pendaftaran->id_user;
            $date = Carbon::now()->format('Ymd');
            $random = rand(1000, 2000);
            $nomor_daftar = ($id_user.$date.$random);

            $riwayat_pasien = new RiwayatPoliklinik();
            $riwayat_pasien->nomor_daftar = $nomor_daftar;
            $riwayat_pasien->nama_pasien = $cari_pendaftaran->nama_pasien;
            $riwayat_pasien->nomor_rekam_medis = $cari_pendaftaran->nomor_rekam_medis;
            $riwayat_pasien->id_poliklinik = $cari_pendaftaran->id_poliklinik;
            $riwayat_pasien->tanggal_daftar = $cari_pendaftaran->kunjungan;
            $riwayat_pasien->resume_medis = null;
            $riwayat_pasien->hasil_penunjang = null;
            $riwayat_pasien->save();

            $response = [];
            $response['nomor_daftar'] = $nomor_daftar;
            $response['nama_pasien'] = $cari_pendaftaran->nama_pasien;
            $response['nomor_rekam_medis'] = $cari_pendaftaran->nomor_rekam_medis;
            $response['id_poliklinik'] = $cari_pendaftaran->id_poliklinik;
            $response['tanggal_daftar'] = $cari_pendaftaran->kunjungan;

            // hapus data pendaftaran poliklinik
            $hapus_pendaftaran_poliklinik = PendaftaranPoliklinik::find($id_pendaftaran);
            $hapus_pendaftaran_poliklinik->delete();

            return ResponseFormatter::success_ok("Pendaftaran Selesai", $response);
        }catch (Exception $e){
            return ResponseFormatter::internal_server_error("Ada Yang Salah Dari Server", $e);
        }
        
    }
}
