<?php

namespace App\Http\Controllers\V1;

use DateTime;
use Exception;
use Carbon\Carbon;
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
use App\Models\V1\NamaPenanggung;
use App\Helpers\ResponseFormatter;
use App\Models\V1\jenis_identitas;
use App\Models\V1\Kewarganegaraan;
use App\Models\V1\PasienSementara;
use App\Http\Controllers\Controller;
use App\Models\V1\KedudukanKeluarga;
use Illuminate\Support\Facades\File;

use App\Models\V1\PendidikanTerakhir;
use function PHPUnit\Framework\isNull;

class AnggotaPasienController extends Controller
{
    public function getAnggotaIndukPasien(Request $request)
    {
        try{
            $email = $request->input('email');

            $user = User::where('email', $email)->first();

            if($user == null) return ResponseFormatter::error_not_found("data tidak ditemukan", null);

            $detail_akun = DetailAkun::getAnggotaInduk($user->kode, $user->id);

            if(count($detail_akun) == 0) return ResponseFormatter::success_ok("Belum Ada Data, Silahkan Buat Data Anggota Keluarga", null);

            $data_anggota = [];
            $response = [];
            foreach($detail_akun as $d){
                if($d->id_pasien == null){
                    $data_pasien_sem = PasienSementara::where('id', $d->id_pasien_temp)->first();
                    if($data_pasien_sem->status_validasi == "2"){
                        $data_anggota['nomor_rekam_medis'] = (string)$data_pasien_sem->id;
                        $data_anggota['nama_anggota'] = $data_pasien_sem->nama;
                        $data_anggota['id_status_validasi'] = "2";
                        $data_anggota['nama_status'] = "Validasi Di Tolak";
                        $response[] = $data_anggota;
                    }else{
                        $data_anggota['nomor_rekam_medis'] = (string)$data_pasien_sem->id;
                        $data_anggota['nama_anggota'] = $data_pasien_sem->nama;
                        $data_anggota['id_status_validasi'] = "0";
                        $data_anggota['nama_status'] = "Sedang di Validasi";
                        $response[] = $data_anggota;
                    }
                }else if($d->id_pasien_temp == null && $d->is_lama == "1"){
                    $data_pasien = Pasien::where('kode', sprintf("%08s", strval($d->id_pasien)))->first();
                    $data_anggota['nomor_rekam_medis'] = sprintf("%08s", strval($data_pasien->kode));
                    $data_anggota['nama_anggota'] = $data_pasien->nama;
                    $data_anggota['id_status_validasi'] = "3";
                    $data_anggota['nama_status'] = "Sedang di Validasi";
                    $response[] = $data_anggota;
                }elseif($d->id_pasien_temp == null){
                    $data_pasien = Pasien::where('kode', sprintf("%08s", strval($d->id_pasien)))->first();
                    $data_anggota['nomor_rekam_medis'] = sprintf("%08s", strval($data_pasien->kode));
                    $data_anggota['nama_anggota'] = $data_pasien->nama;
                    $data_anggota['id_status_validasi'] = "1";
                    $data_anggota['nama_status'] = "Aktif";
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
        try{
            $nomor_rekam_medis = $request->input('nomor_rekam_medis');
            $id_status_validasi = $request->input('id_status_validasi');

            $response = [];

            if($id_status_validasi == 1){
                $pasien = Pasien::where('kode', $nomor_rekam_medis)->first();

                if($pasien == null) return ResponseFormatter::error_not_found("Data Tidak Ditemukan", null);
                //data master fetching
                $agama_kode = Agama::where('kode', $pasien->agama_kode)->first();
                $pendidikan_kode = PendidikanTerakhir::where('kode', $pasien->pendidikan_kode)->first();
                $kewarganegaraan_kode1 = Kewarganegaraan::where('kode', $pasien->kewarganegaraan_kode)->first();
                $jenis_identitas_kode = jenis_identitas::where('kode', $pasien->jenis_identitas_kode)->first();
                $suku_kode = Suku::where('kode', $pasien->suku_kode)->first();
                if($suku_kode == null){
                    $suku = "-";
                }else{
                    $suku = $suku_kode->nama;
                }
                $jenis_kelamin_kode = JenisKelamin::where('kode', $pasien->jkel)->first();
                $status_perkawinan_kode = StatusMenikah::where('kode', $pasien->status_perkawinan)->first();
                $kedudukan_keluarga_kode = KedudukanKeluarga::where('kode', $pasien->kedudukan_keluarga)->first();
                $golongan_darah_kode = GolonganDarah::where('kode', $pasien->golongan_darah)->first();
                $provinsi_kode = Provinsi::where('kode', $pasien->provinsi)->first();
                $kabupaten_kode = KotaKabupaten::where('kode', $pasien->kabupaten)->first();
                $kecamatan_kode = Kecamatan::where('kode', $pasien->kecamatan)->first();
                $jurusan_kode = Jurusan::where('kode', $pasien->jurusan)->first();
                if($jurusan_kode == null){
                    $jurusan = "-";
                }else{
                    $jurusan = $jurusan_kode->nama;
                }
                $penghasilan_kode = Penghasilan::where('kode', $pasien->penghasilan)->first();

                // value
                $rekam_medis = sprintf("%08s", strval($pasien->kode));
                $nomor_identitas = $pasien->no_identitas;
                $jenis_identitas = $jenis_identitas_kode->nama;
                $nama_lengkap = $pasien->nama;
                $tempat_lahir = $pasien->tempat_lahir;
                $kedudukan_keluarga = $kedudukan_keluarga_kode->nama;
                $golongan_darah = $golongan_darah_kode->nama;
                $agama = $agama_kode->agama;
                $nomor_telepon = $pasien->no_telp;
                $jenis_kelamin = $jenis_kelamin_kode->nama;
                $alamat = $pasien->alamat;
                $provinsi = $provinsi_kode->nama;
                $kota_kabupaten = $kabupaten_kode->nama;
                $kecamatan = $kecamatan_kode->nama;
                $status_perkawinan = $status_perkawinan_kode->nama;
                $anak_ke = $pasien->anak_ke;
                $pendidikan_terakhir = $pendidikan_kode->nama;
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

                // umur 
                $tanggal_lahir_db = new DateTime($pasien->tanggal_lahir);
                $tgl_sekarang = new DateTime('today');
                $y = $tgl_sekarang->diff($tanggal_lahir_db)->y;
                $m = $tgl_sekarang->diff($tanggal_lahir_db)->m;
                $d = $tgl_sekarang->diff($tanggal_lahir_db)->d;
                $umur = $y . " tahun " . $m . " bulan " . $d . " hari";

                // tanggal lahir
                $date = Carbon::createFromFormat('Y-m-d', $pasien->tanggal_lahir)->locale('id')->isoFormat('dddd, D MMMM Y ');
                $tanggal_lahir = $date;

            }else if($id_status_validasi == 0){
                $pasien = PasienSementara::where('id', $nomor_rekam_medis)->first();

                if($pasien == null) return ResponseFormatter::error_not_found("Data Tidak Ditemukan", null);

                //data master fetching
                $agama_kode = Agama::where('kode', $pasien->agama_kode)->first();
                $pendidikan_kode = PendidikanTerakhir::where('kode', $pasien->pendidikan_kode)->first();
                $kewarganegaraan_kode1 = Kewarganegaraan::where('kode', $pasien->kewarganegaraan_kode)->first();
                $jenis_identitas_kode = jenis_identitas::where('kode', $pasien->jenis_identitas_kode)->first();
                $suku_kode = Suku::where('kode', $pasien->suku_kode)->first();
                if($suku_kode == null){
                    $suku = "-";
                }else{
                    $suku = $suku_kode->nama;
                }
                $jenis_kelamin_kode = JenisKelamin::where('kode', $pasien->jkel)->first();
                $status_perkawinan_kode = StatusMenikah::where('kode', $pasien->status_perkawinan)->first();
                $kedudukan_keluarga_kode = KedudukanKeluarga::where('kode', $pasien->kedudukan_keluarga)->first();
                $golongan_darah_kode = GolonganDarah::where('kode', $pasien->golongan_darah)->first();
                $provinsi_kode = Provinsi::where('kode', $pasien->provinsi)->first();
                $kabupaten_kode = KotaKabupaten::where('kode', $pasien->kabupaten)->first();
                $kecamatan_kode = Kecamatan::where('kode', $pasien->kecamatan)->first();
                $jurusan_kode = Jurusan::where('kode', $pasien->jurusan)->first();
                if($jurusan_kode == null){
                    $jurusan = "-";
                }else{
                    $jurusan = $jurusan_kode->nama;
                }
                $penghasilan_kode = Penghasilan::where('kode', $pasien->penghasilan)->first();

                // value
                $rekam_medis = null;
                $nomor_identitas = $pasien->no_identitas;
                $jenis_identitas = $jenis_identitas_kode->nama;
                $nama_lengkap = $pasien->nama;
                $tempat_lahir = $pasien->tempat_lahir;
                $kedudukan_keluarga = $kedudukan_keluarga_kode->nama;
                $golongan_darah = $golongan_darah_kode->nama;
                $agama = $agama_kode->agama;
                $nomor_telepon = $pasien->no_telp;
                $jenis_kelamin = $jenis_kelamin_kode->nama;
                $alamat = $pasien->alamat;
                $provinsi = $provinsi_kode->nama;
                $kota_kabupaten = $kabupaten_kode->nama;
                $kecamatan = $kecamatan_kode->nama;
                $status_perkawinan = $status_perkawinan_kode->nama;
                $anak_ke = $pasien->anak_ke;
                $pendidikan_terakhir = $pendidikan_kode->nama;
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

                // umur 
                $tanggal_lahir_db = new DateTime($pasien->tanggal_lahir);
                $tgl_sekarang = new DateTime('today');
                $y = $tgl_sekarang->diff($tanggal_lahir_db)->y;
                $m = $tgl_sekarang->diff($tanggal_lahir_db)->m;
                $d = $tgl_sekarang->diff($tanggal_lahir_db)->d;
                $umur = $y . " tahun " . $m . " bulan " . $d . " hari";

                // tanggal lahir
                $date = Carbon::createFromFormat('Y-m-d', $pasien->tanggal_lahir)->locale('id')->isoFormat('dddd, D MMMM Y ');
                $tanggal_lahir = $date;
            }elseif ($id_status_validasi == 2){
                $pasien = PasienSementara::where('id', $nomor_rekam_medis)->first();

                if($pasien == null) return ResponseFormatter::error_not_found("Data Tidak Ditemukan", null);

                //data master fetching
                $agama_kode = Agama::where('kode', $pasien->agama_kode)->first();
                $pendidikan_kode = PendidikanTerakhir::where('kode', $pasien->pendidikan_kode)->first();
                $kewarganegaraan_kode1 = Kewarganegaraan::where('kode', $pasien->kewarganegaraan_kode)->first();
                $jenis_identitas_kode = jenis_identitas::where('kode', $pasien->jenis_identitas_kode)->first();
                $suku_kode = Suku::where('kode', $pasien->suku_kode)->first();
                if($suku_kode == null){
                    $suku = "-";
                }else{
                    $suku = $suku_kode->nama;
                }
                $jenis_kelamin_kode = JenisKelamin::where('kode', $pasien->jkel)->first();
                $status_perkawinan_kode = StatusMenikah::where('kode', $pasien->status_perkawinan)->first();
                $kedudukan_keluarga_kode = KedudukanKeluarga::where('kode', $pasien->kedudukan_keluarga)->first();
                $golongan_darah_kode = GolonganDarah::where('kode', $pasien->golongan_darah)->first();
                $provinsi_kode = Provinsi::where('kode', $pasien->provinsi)->first();
                $kabupaten_kode = KotaKabupaten::where('kode', $pasien->kabupaten)->first();
                $kecamatan_kode = Kecamatan::where('kode', $pasien->kecamatan)->first();
                $jurusan_kode = Jurusan::where('kode', $pasien->jurusan)->first();
                if($jurusan_kode == null){
                    $jurusan = "-";
                }else{
                    $jurusan = $jurusan_kode->nama;
                }
                $penghasilan_kode = Penghasilan::where('kode', $pasien->penghasilan)->first();

                // value
                $rekam_medis = null;
                $nomor_identitas = $pasien->no_identitas;
                $jenis_identitas = $jenis_identitas_kode->nama;
                $nama_lengkap = $pasien->nama;
                $tempat_lahir = $pasien->tempat_lahir;
                $kedudukan_keluarga = $kedudukan_keluarga_kode->nama;
                $golongan_darah = $golongan_darah_kode->nama;
                $agama = $agama_kode->agama;
                $nomor_telepon = $pasien->no_telp;
                $jenis_kelamin = $jenis_kelamin_kode->nama;
                $alamat = $pasien->alamat;
                $provinsi = $provinsi_kode->nama;
                $kota_kabupaten = $kabupaten_kode->nama;
                $kecamatan = $kecamatan_kode->nama;
                $status_perkawinan = $status_perkawinan_kode->nama;
                $anak_ke = $pasien->anak_ke;
                $pendidikan_terakhir = $pendidikan_kode->nama;
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

                // umur 
                $tanggal_lahir_db = new DateTime($pasien->tanggal_lahir);
                $tgl_sekarang = new DateTime('today');
                $y = $tgl_sekarang->diff($tanggal_lahir_db)->y;
                $m = $tgl_sekarang->diff($tanggal_lahir_db)->m;
                $d = $tgl_sekarang->diff($tanggal_lahir_db)->d;
                $umur = $y . " tahun " . $m . " bulan " . $d . " hari";

                // tanggal lahir
                $date = Carbon::createFromFormat('Y-m-d', $pasien->tanggal_lahir)->locale('id')->isoFormat('dddd, D MMMM Y ');
                $tanggal_lahir = $date;
            }elseif($id_status_validasi == 3){
                $pasien = Pasien::where('kode', $nomor_rekam_medis)->first();

                if($pasien == null) return ResponseFormatter::error_not_found("Data Tidak Ditemukan", null);
                //data master fetching
                $agama_kode = Agama::where('kode', $pasien->agama_kode)->first();
                $pendidikan_kode = PendidikanTerakhir::where('kode', $pasien->pendidikan_kode)->first();
                $kewarganegaraan_kode1 = Kewarganegaraan::where('kode', $pasien->kewarganegaraan_kode)->first();
                $jenis_identitas_kode = jenis_identitas::where('kode', $pasien->jenis_identitas_kode)->first();
                $suku_kode = Suku::where('kode', $pasien->suku_kode)->first();
                if($suku_kode == null){
                    $suku = "-";
                }else{
                    $suku = $suku_kode->nama;
                }
                $jenis_kelamin_kode = JenisKelamin::where('kode', $pasien->jkel)->first();
                $status_perkawinan_kode = StatusMenikah::where('kode', $pasien->status_perkawinan)->first();
                $kedudukan_keluarga_kode = KedudukanKeluarga::where('kode', $pasien->kedudukan_keluarga)->first();
                $golongan_darah_kode = GolonganDarah::where('kode', $pasien->golongan_darah)->first();
                $provinsi_kode = Provinsi::where('kode', $pasien->provinsi)->first();
                $kabupaten_kode = KotaKabupaten::where('kode', $pasien->kabupaten)->first();
                $kecamatan_kode = Kecamatan::where('kode', $pasien->kecamatan)->first();
                $jurusan_kode = Jurusan::where('kode', $pasien->jurusan)->first();
                if($jurusan_kode == null){
                    $jurusan = "-";
                }else{
                    $jurusan = $jurusan_kode->nama;
                }
                $penghasilan_kode = Penghasilan::where('kode', $pasien->penghasilan)->first();

                // value
                $rekam_medis = sprintf("%08s", strval($pasien->kode));
                $nomor_identitas = $pasien->no_identitas;
                $jenis_identitas = $jenis_identitas_kode->nama;
                $nama_lengkap = $pasien->nama;
                $tempat_lahir = $pasien->tempat_lahir;
                $kedudukan_keluarga = $kedudukan_keluarga_kode->nama;
                $golongan_darah = $golongan_darah_kode->nama;
                $agama = $agama_kode->agama;
                $nomor_telepon = $pasien->no_telp;
                $jenis_kelamin = $jenis_kelamin_kode->nama;
                $alamat = $pasien->alamat;
                $provinsi = $provinsi_kode->nama;
                $kota_kabupaten = $kabupaten_kode->nama;
                $kecamatan = $kecamatan_kode->nama;
                $status_perkawinan = $status_perkawinan_kode->nama;
                $anak_ke = $pasien->anak_ke;
                $pendidikan_terakhir = $pendidikan_kode->nama;
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

                // umur 
                $tanggal_lahir_db = new DateTime($pasien->tanggal_lahir);
                $tgl_sekarang = new DateTime('today');
                $y = $tgl_sekarang->diff($tanggal_lahir_db)->y;
                $m = $tgl_sekarang->diff($tanggal_lahir_db)->m;
                $d = $tgl_sekarang->diff($tanggal_lahir_db)->d;
                $umur = $y . " tahun " . $m . " bulan " . $d . " hari";

                // tanggal lahir
                $date = Carbon::createFromFormat('Y-m-d', $pasien->tanggal_lahir)->locale('id')->isoFormat('dddd, D MMMM Y ');
                $tanggal_lahir = $date;
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
            $response['pekerjaan'] = $pekerjaan_kode;
            $response['kewarganegaraan'] = $kewarganegaraan_kode;
            $response['nama_pasangan'] = $nama_pasangan;
            $response['nama_ayah'] = $nama_ayah;
            $response['nomor_rekam_medis_ayah'] = $nomor_rekam_medis_ayah;
            $response['nama_ibu'] = $nama_ibu;
            $response['nomor_rekam_medis_ibu'] = $nomor_rekam_medis_ibu;
            $response['alergi'] = $alergi;
            return ResponseFormatter::success_ok("Berhasil Mendapatkan Data Pasien", $response);
        }catch (Exception $e){
            return ResponseFormatter::internal_server_error("Kesalahan Dari Server", $e);
        }
    }

    public function unlinkAnggotaIndukPasien(Request $request)
    {
        try {
            $nomor_rekam_medis = $request->nomor_rekam_medis;
            $no_rm = (int)$nomor_rekam_medis;

            //cek anggota
            $detail_akun = DetailAkun::where('id_pasien', $no_rm)->first();
            $hapus_detail_akun = DetailAkun::find($detail_akun->id);

            //cek penanggung
            $penanggung = Penanggung::where('pasien_id', (int)$nomor_rekam_medis)->first();
            $foto_penanggung = $penanggung->foto_kartu_penanggung;
            $hapus_penanggung = Penanggung::find($penanggung->id);

            //cek foto pasien
            $foto_pasien = FotoPasien::where('id_pasien', (int)$nomor_rekam_medis)->first();
            $foto_swa = $foto_pasien->foto_swa_pasien;
            $foto_kartu_pasien = $foto_pasien->foto_kartu_identitas_pasien;
            $hapus_foto_pasien = FotoPasien::find($foto_pasien->id);

            //hapus data detail akun
            $hapus_detail_akun->delete();

            //hapus data penanggung
            if(File::exists(public_path($foto_penanggung))){
                File::delete(public_path($foto_penanggung));
                $hapus_penanggung->delete();
                return ResponseFormatter::success_ok("Berhasil Mengahapus Penanggung", null);
            }else{
                return ResponseFormatter::error_not_found("Foto Penanggung Tidak Ada", null);
            }

            //hapus data foto pasien
            if(File::exists(public_path($foto_swa)) && File::exists(public_path($foto_kartu_pasien))){
                File::delete(public_path($foto_swa));
                File::delete(public_path($foto_kartu_pasien));
                $hapus_foto_pasien->delete();
                return ResponseFormatter::success_ok("Berhasil Mengahapus Penanggung", null);
            }else{
                return ResponseFormatter::error_not_found("Foto Penanggung Tidak Ada", null);
            }
        } catch (\Throwable $th) {
            return ResponseFormatter::internal_server_error("Kesalahan Dari Server", $th);
        }
        
    }

    public function listPenanggungAnggota(Request $request)
    {
        $nomor_rekam_medis_input = $request->input('nomor_rekam_medis');
        $id_status_validasi_input = $request->input('id_status_validasi');

        if($id_status_validasi_input == "0"){
            $penanggung = Penanggung::where('id_pasien_temp', $nomor_rekam_medis_input)->where('nama_penanggung', '!=', '1')->get();
            $list_penanggung = [];
            $response = [];
            foreach($penanggung as $p){
                //ambil nama
                if($p->id_pasien_temp == null){
                    $pasien = Pasien::where('kode', sprintf("%08s", strval($p->pasien_id)))->first();
                    $nama_pasien = $pasien->nama;
                }elseif($p->pasien_id == null){
                    $pasien_sementara = PasienSementara::where('id', $p->id_pasien_temp)->first();
                    $nama_pasien = $pasien_sementara->nama;
                }

                // ambil data nama penanggung
                $nam_pen = NamaPenanggung::where('kode', $p->nama_penanggung)->first();
                $nama_penanggung = $nam_pen->nama;

                //set foto penanggung
                if($p->nama_penanggung == "2"){
                    $foto_penanggung = "/foto_penanggung/bpjs.png";
                }elseif($p->nama_penanggung == "3"){
                    $foto_penanggung = "/foto_penanggung/kis.png";
                }elseif($p->nama_penanggung == "4"){
                    $foto_penanggung = "/foto_penanggung/jamkesda.png";
                }

                $list_penanggung['nomor_rekam_medis'] = $p->id;
                $list_penanggung['nama_pasien'] = $nama_pasien;
                $list_penanggung['nama_penanggung'] = $nama_penanggung;
                $list_penanggung['nomor_kartu'] = $p->nomor_kartu_penanggung;
                $list_penanggung['foto_penanggung'] = $p->foto_kartu_penanggung;
                $list_penanggung['foto_kartu'] = $foto_penanggung;
                $response[] = $list_penanggung;
            }
        }elseif($id_status_validasi_input == "1"){
            $penanggung = Penanggung::where('pasien_id', $nomor_rekam_medis_input)->get();
            $list_penanggung = [];
            $response = [];
            foreach($penanggung as $p){
                //ambil nama
                if($p->id_pasien_temp == null){
                    $pasien = Pasien::where('kode', sprintf("%08s", strval($p->pasien_id)))->first();
                    $nama_pasien = $pasien->nama;
                }elseif($p->pasien_id == null){
                    $pasien_sementara = PasienSementara::where('id', $p->id_pasien_temp)->first();
                    $nama_pasien = $pasien_sementara->nama;
                }

                // ambil data nama penanggung
                $nam_pen = NamaPenanggung::where('kode', $p->nama_penanggung)->first();
                $nama_penanggung = $nam_pen->nama;

                //set foto penanggung
                if($p->nama_penanggung == "2"){
                    $foto_penanggung = "/foto_penanggung/bpjs.png";
                }elseif($p->nama_penanggung == "3"){
                    $foto_penanggung = "/foto_penanggung/kis.png";
                }elseif($p->nama_penanggung == "4"){
                    $foto_penanggung = "/foto_penanggung/jamkesda.png";
                }

                $list_penanggung['nomor_rekam_medis'] = $p->id;
                $list_penanggung['nama_pasien'] = $nama_pasien;
                $list_penanggung['nama_penanggung'] = $nama_penanggung;
                $list_penanggung['nomor_kartu'] = $p->nomor_kartu_penanggung;
                $list_penanggung['foto_penanggung'] = $p->foto_kartu_penanggung;
                $list_penanggung['foto_kartu'] = $foto_penanggung;
                $response[] = $list_penanggung;
            }
        }elseif($id_status_validasi_input == "3"){
            $penanggung = Penanggung::where('pasien_id', $nomor_rekam_medis_input)->get();
            $list_penanggung = [];
            $response = [];
            foreach($penanggung as $p){
                //ambil nama
                if($p->id_pasien_temp == null){
                    $pasien = Pasien::where('kode', sprintf("%08s", strval($p->pasien_id)))->first();
                    $nama_pasien = $pasien->nama;
                }elseif($p->pasien_id == null){
                    $pasien_sementara = PasienSementara::where('id', $p->id_pasien_temp)->first();
                    $nama_pasien = $pasien_sementara->nama;
                }

                // ambil data nama penanggung
                $nam_pen = NamaPenanggung::where('kode', $p->nama_penanggung)->first();
                $nama_penanggung = $nam_pen->nama;

                //set foto penanggung
                if($p->nama_penanggung == "2"){
                    $foto_penanggung = "/foto_penanggung/bpjs.png";
                }elseif($p->nama_penanggung == "3"){
                    $foto_penanggung = "/foto_penanggung/kis.png";
                }elseif($p->nama_penanggung == "4"){
                    $foto_penanggung = "/foto_penanggung/jamkesda.png";
                }

                $list_penanggung['nomor_rekam_medis'] = $p->id;
                $list_penanggung['nama_pasien'] = $nama_pasien;
                $list_penanggung['nama_penanggung'] = $nama_penanggung;
                $list_penanggung['nomor_kartu'] = $p->nomor_kartu_penanggung;
                $list_penanggung['foto_penanggung'] = $p->foto_kartu_penanggung;
                $list_penanggung['foto_kartu'] = $foto_penanggung;
                $response[] = $list_penanggung;
            }
        }

        return ResponseFormatter::success_ok("Berhasil Mendapatkan Data", $response);
    }
}
