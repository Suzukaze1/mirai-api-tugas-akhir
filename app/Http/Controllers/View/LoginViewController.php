<?php

namespace App\Http\Controllers\View;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Admin;
use App\Models\V1\Suku;
use App\Mail\MyTestMail;
use App\Models\V1\Agama;
use App\Models\V1\Notif;
use App\Models\V1\Pasien;
use App\Models\V1\Jurusan;
use App\Models\V1\Provinsi;
use App\Models\V1\Kecamatan;
use Illuminate\Http\Request;
use App\Helpers\Notification;
use App\Models\V1\DetailAkun;
use App\Models\V1\FotoPasien;
use App\Models\V1\Penanggung;
use App\Models\V1\Penghasilan;
use App\Models\V1\JenisKelamin;
use App\Models\V1\GolonganDarah;
use App\Models\V1\KotaKabupaten;
use App\Models\V1\StatusMenikah;
use App\Models\V1\jenis_identitas;
use App\Models\V1\Kewarganegaraan;
use App\Models\V1\PasienSementara;
use App\Http\Controllers\Controller;
use App\Models\V1\KedudukanKeluarga;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use App\Models\V1\PendidikanTerakhir;
use Illuminate\Support\Facades\Session;

class LoginViewController extends Controller
{
    public function index()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $username = $request->username;
        $password = $request->password;

        if($login = Admin::where(['nip' => $username])->first()){
            if($login->password == $password){
                Session::put('login',true);
                Session::put('nama_lengkap',$login->nama);
            }else{
                redirect('/')->with('pesan', 'Password Salah');
            }
        }else{
            redirect("/")->with('pesan', 'User Tidak DItemukan');
        }
        return redirect('/home');
    }

    public function logout()
    {
        Session::flush();
        return redirect('/');
    }

    public function home()
    {
        return view('home');
    }

    public function listPasien()
    {
        $pasien = PasienSementara::all();
        return view('verifikasi_pasien', ['pasien' => $pasien]);
    }

    public function validasiPasien(Request $request, $id)
    {
        $pasien = PasienSementara::where('id', $id)->get();
        $agama = Agama::where('kode', $pasien[0]->agama_kode)->get();
        $pendidikan_terakhir = PendidikanTerakhir::where('kode', $pasien[0]->pendidikan_kode)->get();
        $kewarganegaraan_kode = Kewarganegaraan::where('kode', $pasien[0]->kewarganegaraan_kode)->get();
        $jenis_identitas_kode = jenis_identitas::where('kode', $pasien[0]->jenis_identitas_kode)->get();
        $jenis_kelamin = JenisKelamin::where('kode', $pasien[0]->jkel)->get();
        $status_perkawinan = StatusMenikah::where('kode', $pasien[0]->status_perkawinan)->get();
        $kedudukan_keluarga = KedudukanKeluarga::where('kode', $pasien[0]->kedudukan_keluarga)->get();
        $golongan_darah = GolonganDarah::where('kode', $pasien[0]->golongan_darah)->get();
        $provinsi = Provinsi::where('kode', $pasien[0]->provinsi)->get();
        $kabupaten = KotaKabupaten::where('kode', $pasien[0]->kabupaten)->get();
        $kecamatan = Kecamatan::where('kode', $pasien[0]->kecamatan)->get();
        $penghasilan = Penghasilan::where('kode', $pasien[0]->penghasilan)->get();
        $penanggung = Penanggung::where('id_pasien_temp', $pasien[0]->id)->get();
        $foto_pasien = FotoPasien::where('id_pasien_temp', $pasien[0]->id)->get();
        $akun = User::where('id_pasien_temp', $pasien[0]->id)->get();

        // bisa null
        $jurusan = Jurusan::where('kode', $pasien[0]->jurusan)->first();
        $suku_kode = Suku::where('kode', $pasien[0]->suku_kode)->first();
        
        return view('validasi_pasien', 
                    ['pasien' => $pasien, 
                    'agama' => $agama,
                    'pendidikan_terakhir' => $pendidikan_terakhir,
                    'kewarganegaraan_kode' => $kewarganegaraan_kode,
                    'jenis_identitas_kode' => $jenis_identitas_kode,
                    'suku_kode' => $suku_kode,
                    'jenis_kelamin' => $jenis_kelamin,
                    'status_perkawinan' => $status_perkawinan,
                    'kedudukan_keluarga' => $kedudukan_keluarga,
                    'golongan_darah' => $golongan_darah,
                    'provinsi' => $provinsi,
                    'kabupaten' => $kabupaten,
                    'kecamatan' => $kecamatan,
                    'jurusan' => $jurusan,
                    'penghasilan' => $penghasilan,
                    'penanggung' => $penanggung,
                    'foto_pasien' => $foto_pasien,
                    'akun' => $akun]);
    }

    public function verifikasiPasien(Request $request)
    {
        $id_akun = $request->id;
        $id_status_validasi = $request->id_status_validasi;
        $alasan_berhasil_gagal = $request->alasan_berhasil_gagal;

        // hari
        $hari = Carbon::now()->format('Y-m-d H:i:s');

        if($id_status_validasi == 1)
        {
            $title = "Verifikasi Pasien Berhasil";
            $pesan = "Data Pasien Berhasil Di Verifikasi";
        }
        else if($id_status_validasi == 2)
        {
            $title = "Verifikasi Pasien Gagal";
            $pesan = "Data Pasien Gagal Di Verifikasi";
        }

        if($id_status_validasi == 1)
        {
            // ambil data pasien temp dari id akun
            $akun = User::where('id', $id_akun)->first();
            $list_pasien = PasienSementara::where('id', $akun->id_pasien_temp)->first();

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

            // input data
            $pasien = new Pasien();
            $pasien->kode = $nomor_rekam_medik;
            $pasien->no_identitas = $list_pasien->no_identitas;
            $pasien->jenis_identitas_kode = $list_pasien->jenis_identitas_kode;
            $pasien->nama = $list_pasien->nama;
            $pasien->tempat_lahir = $list_pasien->tempat_lahir;
            $pasien->tanggal_lahir = $list_pasien->tanggal_lahir;
            $pasien->kedudukan_keluarga = $list_pasien->kedudukan_keluarga;
            $pasien->golongan_darah = $list_pasien->golongan_darah;
            $pasien->agama_kode = $list_pasien->agama_kode;
            $pasien->suku_kode = $list_pasien->suku_kode;
            $pasien->no_telp = $list_pasien->no_telp;
            $pasien->jkel = $list_pasien->jkel;
            $pasien->alamat = $list_pasien->alamat;
            $pasien->provinsi = $list_pasien->provinsi;
            $pasien->kabupaten = $list_pasien->kabupaten;
            $pasien->kecamatan = $list_pasien->kecamatan;
            $pasien->status_perkawinan = $list_pasien->status_perkawinan;
            $pasien->umur = $list_pasien->umur;
            $pasien->anak_ke = $list_pasien->anak_ke;
            $pasien->pendidikan_kode = $list_pasien->pendidikan_kode;
            $pasien->jurusan = $list_pasien->jurusan;
            $pasien->nama_tempat_bekerja = $list_pasien->nama_tempat_bekerja;
            $pasien->alamat_tempat_bekerja = $list_pasien->alamat_tempat_bekerja;
            $pasien->penghasilan = $list_pasien->penghasilan;
            $pasien->pekerjaan_kode = $list_pasien->pekerjaan_kode;
            $pasien->kewarganegaraan_kode = $list_pasien->kewarganegaraan_kode;
            $pasien->nama_pasangan = $list_pasien->nama_pasangan;
            $pasien->ayah_nama = $list_pasien->ayah_nama;
            $pasien->no_rekam_medik_ayah = $list_pasien->no_rekam_medik_ayah;
            $pasien->ibu_nama = $list_pasien->ibu_nama;
            $pasien->no_rekam_medik_ibu = $list_pasien->no_rekam_medik_ibu;
            $pasien->alergi = $list_pasien->alergi;

            // update table users atau akun
            $user = User::find($id_akun);
            $user->id_pasien_temp = null;
            $user->kode = (int)$nomor_rekam_medik;

            // cari dan update detail akun 
            $cari_detail_akun = DetailAkun::where('id_pasien_temp', $akun->id_pasien_temp)->first();
            $detail_akun = DetailAkun::find($cari_detail_akun->id);
            $detail_akun->id_pasien_temp = null;
            $detail_akun->id_pasien = (int)$nomor_rekam_medik;

            // cari dan update foto pasien
            $cari_foto_pasien = FotoPasien::where('id_pasien_temp', $akun->id_pasien_temp)->first();
            $foto_pasien = FotoPasien::find($cari_foto_pasien->id);
            $foto_pasien->id_pasien_temp = null;
            $foto_pasien->id_pasien = (int)$nomor_rekam_medik;

            // cari dan update penanggung
            $cari_penanggung = Penanggung::where('id_pasien_temp', $akun->id_pasien_temp)->get();
            foreach($cari_penanggung as $cp)
            {
                $penanggung = Penanggung::find($cp->id);
                $penanggung->id_pasien_temp = null;
                $penanggung->pasien_id = (int)$nomor_rekam_medik;
                $penanggung->save();
            }

            //simpan semua data
            $pasien->save();
            $user->save();
            $detail_akun->save();
            $foto_pasien->save();

            // hapus data di tabel pasien sementara
            $hapus_pasien_sementara = PasienSementara::find($list_pasien->id);
            $hapus_pasien_sementara->delete();

            $details = [
                'title' => $title,
                'body' => $alasan_berhasil_gagal,
                'otp' => '',
                'hash_otp' => ''
            ];

            Mail::to($akun->email)->send(new MyTestMail($details));

            // cari email
            $user_email = User::where('id', $id_akun)->first();

            Notification::sendNotification($user_email->firebase_token, 'Berhasil Divalidasi', 'Data Pasien Berhasil Divalidasi', null);  

            //simpan data ke tb notif
            $notif = new Notif();
            $notif->email = $user_email->email;
            $notif->subjek = $title;
            $notif->isi = $alasan_berhasil_gagal;
            $notif->waktu = $hari;
            $notif->save();

            return redirect('list-pasien-baru')->with('pesan', $pesan);
            
        }
        else if($id_status_validasi == 2)
        {
            // ambil data pasien temp dari id akun
            $akun = User::where('id', $id_akun)->first();
            $list_pasien = PasienSementara::where('id', $akun->id_pasien_temp)->first();

            // update data di tabel pasien sementara
            $update_pasien_sementara = PasienSementara::find($list_pasien->id);
            $update_pasien_sementara->status_validasi = "2";
            $update_pasien_sementara->save();

            //cari foto pasien
            $foto_pasien_tb = FotoPasien::where('id_pasien_temp', $list_pasien->id)->first();
            $foto_swa = $foto_pasien_tb->foto_swa_pasien;
            $foto_kartu_pasien = $foto_pasien_tb->foto_kartu_identitas_pasien;
            //hapus data foto pasien
            $hapus_foto_pasien = FotoPasien::find($foto_pasien_tb->id);
            if(File::exists(public_path($foto_swa)) && File::exists(public_path($foto_kartu_pasien))){
                File::delete(public_path($foto_swa));
                File::delete(public_path($foto_kartu_pasien));
                $hapus_foto_pasien->delete();
            }else{
                $hapus_foto_pasien->delete();
            }

            //cari penanggung
            $foto_penanggung_tb = Penanggung::where('id_pasien_temp', $list_pasien->id)->get();
            foreach($foto_penanggung_tb as $fpt)
            {
                $foto_penanggung = $fpt->foto_kartu_penanggung;
                $hapus_penanggung = Penanggung::find($fpt->id);
                //hapus data penanggung
                if(File::exists(public_path($foto_penanggung))){
                    File::delete(public_path($foto_penanggung));
                    $hapus_penanggung->delete();
                }
            }

            $pesan = "Verifikasi data pasien di tolak";

            // cari email
            $user_email = User::where('id', $id_akun)->first();

            // firebase
            Notification::sendNotification($user_email->firebase_token, 'Gagal Divalidasi', 'Data Pasien Ditolak', null);  

            //simpan data ke tb notif
            $notif = new Notif();
            $notif->email = $user_email->email;
            $notif->subjek = $title;
            $notif->isi = $alasan_berhasil_gagal;
            $notif->waktu = $hari;
            $notif->save();

            return redirect('/list-pasien-baru')->with('pesangagal', $pesan);
        }
    }

    public function listPasienLama()
    {
        $cek_pasien_lama = DetailAkun::where('is_lama', '1')->where('is_anggota', '!=', '1')->orWhere('is_lama', '2')->get();

        $response = [];
        $array = [];

        foreach($cek_pasien_lama as $cpl){
            $pasien = Pasien::where('kode', sprintf("%08s", strval($cpl->id_pasien)))->first();
            $array['kode'] = $pasien->kode;
            $array['nama_lengkap'] = $pasien->nama;
            $array['jenis_identitas_kode'] = $pasien->jenis_identitas_kode;
            $array['no_identitas'] = $pasien->no_identitas;
            $array['is_lama'] = $cpl->is_lama; 
            $response[] = $array;
        }
        return view('verifikasi_pasien_lama', ['pasien' => $response]);
    }

    public function validasiPasienLama(Request $request, $id)
    {
        $pasien = Pasien::where('kode', sprintf("%08s", strval($id)))->get();
        $agama = Agama::where('kode', $pasien[0]->agama_kode)->get();
        $pendidikan_terakhir = PendidikanTerakhir::where('kode', $pasien[0]->pendidikan_kode)->get();
        $kewarganegaraan_kode = Kewarganegaraan::where('kode', $pasien[0]->kewarganegaraan_kode)->get();
        $jenis_identitas_kode = jenis_identitas::where('kode', $pasien[0]->jenis_identitas_kode)->get();
        $jenis_kelamin = JenisKelamin::where('kode', $pasien[0]->jkel)->get();
        $status_perkawinan = StatusMenikah::where('kode', $pasien[0]->status_perkawinan)->get();
        $kedudukan_keluarga = KedudukanKeluarga::where('kode', $pasien[0]->kedudukan_keluarga)->get();
        $golongan_darah = GolonganDarah::where('kode', $pasien[0]->golongan_darah)->get();
        $provinsi = Provinsi::where('kode', $pasien[0]->provinsi)->get();
        $kabupaten = KotaKabupaten::where('kode', $pasien[0]->kabupaten)->get();
        $kecamatan = Kecamatan::where('kode', $pasien[0]->kecamatan)->get();
        $penghasilan = Penghasilan::where('kode', $pasien[0]->penghasilan)->get();
        $penanggung = Penanggung::where('pasien_id', $pasien[0]->kode)->where('nama_penanggung', '!=', '1')->get();
        $foto_pasien = FotoPasien::where('id_pasien', $pasien[0]->kode)->get();
        $akun = User::where('kode', $pasien[0]->kode)->get();

        // bisa null
        $jurusan = Jurusan::where('kode', $pasien[0]->jurusan)->first();
        $suku_kode = Suku::where('kode', $pasien[0]->suku_kode)->first();
        
        return view('validasi_pasien_lama', 
                    ['pasien' => $pasien, 
                    'agama' => $agama,
                    'pendidikan_terakhir' => $pendidikan_terakhir,
                    'kewarganegaraan_kode' => $kewarganegaraan_kode,
                    'jenis_identitas_kode' => $jenis_identitas_kode,
                    'suku_kode' => $suku_kode,
                    'jenis_kelamin' => $jenis_kelamin,
                    'status_perkawinan' => $status_perkawinan,
                    'kedudukan_keluarga' => $kedudukan_keluarga,
                    'golongan_darah' => $golongan_darah,
                    'provinsi' => $provinsi,
                    'kabupaten' => $kabupaten,
                    'kecamatan' => $kecamatan,
                    'jurusan' => $jurusan,
                    'penghasilan' => $penghasilan,
                    'penanggung' => $penanggung,
                    'foto_pasien' => $foto_pasien,
                    'akun' => $akun]);
    }

    public function verifikasiPasienLama(Request $request)
    {
        $id_akun = $request->id;
        $id_status_validasi = $request->id_status_validasi;
        $alasan_berhasil_gagal = $request->alasan_berhasil_gagal;

        // ambil data akun
        $akun = User::where('id', $id_akun)->first();

        // hari
        $hari = Carbon::now()->format('Y-m-d H:i:s');

        if($id_status_validasi == 1)
        {
            $detail_akun = DetailAkun::where('id_akun', $id_akun)->first();
            $ubah_status = DetailAkun::find($detail_akun->id);
            $ubah_status->is_lama = null;
            $ubah_status->save();

            $subjek = "Verifikasi Pasien ".$akun->name." Berhasil";
            $pesan_notif_web = "Data Pasien Berhasil Di Verifikasi";

            $details = [
                'title' => $subjek,
                'body' => $alasan_berhasil_gagal,
                'otp' => '',
                'hash_otp' => ''
            ];

            Mail::to($akun->email)->send(new MyTestMail($details));

            Notification::sendNotification($akun->firebase_token, 'Berhasil Divalidasi', 'Data Pasien Berhasil Divalidasi', null);

            //simpan data ke tb notif
            $notif = new Notif();
            $notif->email = $akun->email;
            $notif->subjek = $subjek;
            $notif->isi = $alasan_berhasil_gagal;
            $notif->waktu = $hari;
            $notif->save();

            return redirect('list-pasien-lama')->with('pesan', $pesan_notif_web);
        }
        elseif($id_status_validasi == 2)
        {
            $detail_akun = DetailAkun::where('id_akun', $id_akun)->first();
            $ubah_status = DetailAkun::find($detail_akun->id);
            $ubah_status->is_lama = '2';
            $ubah_status->save();

            $subjek = "Verifikasi Pasien ".$akun->name." ditolak";
            $pesan_notif_web = "Verifikasi Pasien ditolak";

            $details = [
                'title' => $subjek,
                'body' => $alasan_berhasil_gagal,
                'otp' => '',
                'hash_otp' => ''
            ];

            Mail::to($akun->email)->send(new MyTestMail($details));

            //cari foto pasien
            $foto_pasien_tb = FotoPasien::where('id_pasien', $akun->kode)->first();
            $foto_swa = $foto_pasien_tb->foto_swa_pasien;
            $foto_kartu_pasien = $foto_pasien_tb->foto_kartu_identitas_pasien;

            //hapus data foto pasien
            $hapus_foto_pasien = FotoPasien::find($foto_pasien_tb->id);
            if(File::exists(public_path($foto_swa)) && File::exists(public_path($foto_kartu_pasien))){
                File::delete(public_path($foto_swa));
                File::delete(public_path($foto_kartu_pasien));
                $hapus_foto_pasien->delete();
            }

            //cari penanggung
            $foto_penanggung_tb = Penanggung::where('pasien_id', $akun->kode)->get();
            foreach($foto_penanggung_tb as $fpt)
            {
                $foto_penanggung = $fpt->foto_kartu_penanggung;
                $hapus_penanggung = Penanggung::find($fpt->id);
                //hapus data penanggung
                if(File::exists(public_path($foto_penanggung))){
                    File::delete(public_path($foto_penanggung));
                    $hapus_penanggung->delete();
                }
            }

            Notification::sendNotification($akun->firebase_token, 'Gagal Divalidasi', 'Data Pasien Ditolak', null);  

            //simpan data ke tb notif
            $notif = new Notif();
            $notif->email = $akun->email;
            $notif->subjek = $subjek;
            $notif->isi = $alasan_berhasil_gagal;
            $notif->waktu = $hari;
            $notif->save();

            return redirect('list-pasien-lama')->with('pesangagal', $pesan_notif_web);
        }
    }

    public function listAnggotaPasienLama()
    {
        $cek_pasien_lama = DetailAkun::where('is_lama', '1')->where('is_anggota', '1')->orWhere('is_lama', '2')->get();

        $response = [];
        $array = [];

        foreach($cek_pasien_lama as $cpl){
            $pasien = Pasien::where('kode', sprintf("%08s", strval($cpl->id_pasien)))->first();
            $array['kode'] = $pasien->kode;
            $array['nama_lengkap'] = $pasien->nama;
            $array['jenis_identitas_kode'] = $pasien->jenis_identitas_kode;
            $array['no_identitas'] = $pasien->no_identitas;
            $array['is_lama'] = $cpl->is_lama; 
            $response[] = $array;
        }
        return view('verifikasi_anggota_pasien_lama', ['pasien' => $response]);
    }

    public function validasiAnggotaPasienLama(Request $request, $id)
    {
        $pasien = Pasien::where('kode', sprintf("%08s", strval($id)))->get();
        $detail_akun = DetailAkun::where('id_pasien', $id)->first();
        $agama = Agama::where('kode', $pasien[0]->agama_kode)->get();
        $pendidikan_terakhir = PendidikanTerakhir::where('kode', $pasien[0]->pendidikan_kode)->get();
        $kewarganegaraan_kode = Kewarganegaraan::where('kode', $pasien[0]->kewarganegaraan_kode)->get();
        $jenis_identitas_kode = jenis_identitas::where('kode', $pasien[0]->jenis_identitas_kode)->get();
        $jenis_kelamin = JenisKelamin::where('kode', $pasien[0]->jkel)->get();
        $status_perkawinan = StatusMenikah::where('kode', $pasien[0]->status_perkawinan)->get();
        $kedudukan_keluarga = KedudukanKeluarga::where('kode', $pasien[0]->kedudukan_keluarga)->get();
        $golongan_darah = GolonganDarah::where('kode', $pasien[0]->golongan_darah)->get();
        $provinsi = Provinsi::where('kode', $pasien[0]->provinsi)->get();
        $kabupaten = KotaKabupaten::where('kode', $pasien[0]->kabupaten)->get();
        $kecamatan = Kecamatan::where('kode', $pasien[0]->kecamatan)->get();
        $penghasilan = Penghasilan::where('kode', $pasien[0]->penghasilan)->get();
        $penanggung = Penanggung::where('pasien_id', $pasien[0]->kode)->where('nama_penanggung', '!=', '1')->get();
        $foto_pasien = FotoPasien::where('id_pasien', $pasien[0]->kode)->get();
        $akun = User::where('id', $detail_akun->id_akun)->get();

        // bisa null
        $jurusan = Jurusan::where('kode', $pasien[0]->jurusan)->first();
        $suku_kode = Suku::where('kode', $pasien[0]->suku_kode)->first();
        
        return view('validasi_anggota_pasien_lama', 
                    ['pasien' => $pasien, 
                    'agama' => $agama,
                    'pendidikan_terakhir' => $pendidikan_terakhir,
                    'kewarganegaraan_kode' => $kewarganegaraan_kode,
                    'jenis_identitas_kode' => $jenis_identitas_kode,
                    'suku_kode' => $suku_kode,
                    'jenis_kelamin' => $jenis_kelamin,
                    'status_perkawinan' => $status_perkawinan,
                    'kedudukan_keluarga' => $kedudukan_keluarga,
                    'golongan_darah' => $golongan_darah,
                    'provinsi' => $provinsi,
                    'kabupaten' => $kabupaten,
                    'kecamatan' => $kecamatan,
                    'jurusan' => $jurusan,
                    'penghasilan' => $penghasilan,
                    'penanggung' => $penanggung,
                    'foto_pasien' => $foto_pasien,
                    'akun' => $akun]);
    }

    public function verifikasiAnggotaPasienLama(Request $request)
    {
        $id_akun = $request->id;
        $nomor_rekam_medis = $request->kode;
        $id_status_validasi = $request->id_status_validasi;
        $alasan_berhasil_gagal = $request->alasan_berhasil_gagal;

        // ambil data akun
        $akun = User::where('id', $id_akun)->first();
        $pasien = Pasien::where('kode', sprintf("%08s", strval($nomor_rekam_medis)))->first();

        $hari = Carbon::now()->format('Y-m-d H:i:s');

        if($id_status_validasi == 1)
        {
            $detail_akun = DetailAkun::where('id_akun', $id_akun)->first();
            $ubah_status = DetailAkun::find($detail_akun->id);
            $ubah_status->is_lama = null;
            $ubah_status->save();

            $subjek = "Verifikasi Anggota Pasien ".$pasien->nama." Berhasil";
            $pesan_notif_web = "Data Anggota Pasien Berhasil Di Verifikasi";

            $details = [
                'title' => $subjek,
                'body' => $alasan_berhasil_gagal,
                'otp' => '',
                'hash_otp' => ''
            ];

            Mail::to($akun->email)->send(new MyTestMail($details));

            Notification::sendNotification($akun->firebase_token, 'Berhasil Divalidasi', 'Data Pasien Berhasil Divalidasi', null);

            //simpan data ke tb notif
            $notif = new Notif();
            $notif->email = $akun->email;
            $notif->subjek = $subjek;
            $notif->isi = $alasan_berhasil_gagal;
            $notif->waktu = $hari;
            $notif->save();

            return redirect('list-anggota-pasien-lama')->with('pesan', $pesan_notif_web);
        }
        elseif($id_status_validasi == 2)
        {
            $detail_akun = DetailAkun::where('id_akun', $id_akun)->first();
            $ubah_status = DetailAkun::find($detail_akun->id);
            $ubah_status->is_lama = '2';
            $ubah_status->save();

            $subjek = "Verifikasi Anggota Pasien ".$pasien->nama." ditolak";
            $pesan_notif_web = "Verifikasi Anggota Pasien ditolak";

            $details = [
                'title' => $subjek,
                'body' => $alasan_berhasil_gagal,
                'otp' => '',
                'hash_otp' => ''
            ];

            Mail::to($akun->email)->send(new MyTestMail($details));

            //cari foto pasien
            $foto_pasien_tb = FotoPasien::where('id_pasien', $akun->kode)->first();
            $foto_swa = $foto_pasien_tb->foto_swa_pasien;
            $foto_kartu_pasien = $foto_pasien_tb->foto_kartu_identitas_pasien;
            //hapus data foto pasien
            $hapus_foto_pasien = FotoPasien::find($foto_pasien_tb->id);
            if(File::exists(public_path($foto_swa)) && File::exists(public_path($foto_kartu_pasien))){
                File::delete(public_path($foto_swa));
                File::delete(public_path($foto_kartu_pasien));
                $hapus_foto_pasien->delete();
            }else{
                $hapus_foto_pasien->delete();
            }

            //cari penanggung
            $foto_penanggung_tb = Penanggung::where('pasien_id', $akun->kode)->get();
            foreach($foto_penanggung_tb as $fpt)
            {
                $foto_penanggung = $fpt->foto_kartu_penanggung;
                $hapus_penanggung = Penanggung::find($fpt->id);
                //hapus data penanggung
                if(File::exists(public_path($foto_penanggung))){
                    File::delete(public_path($foto_penanggung));
                    $hapus_penanggung->delete();
                }
            }

            Notification::sendNotification($akun->firebase_token, 'Gagal Divalidasi', 'Data Pasien Ditolak', null);

            //simpan data ke tb notif
            $notif = new Notif();
            $notif->email = $akun->email;
            $notif->subjek = $subjek;
            $notif->isi = $alasan_berhasil_gagal;
            $notif->waktu = $hari;
            $notif->save();

            return redirect('list-anggota-pasien-lama')->with('pesangagal', $pesan_notif_web);
        }
    }

    public function listAnggotaPasienBaru()
    {
        $cek_pasien_lama = DetailAkun::where('is_anggota', '1')->get();

        $response = [];
        $array = [];

        foreach($cek_pasien_lama as $cpl){
            $pasien = PasienSementara::where('id', $cpl->id_pasien_temp)->get();
            foreach($pasien as $p){
                $array['kode'] = $p->id;
                $array['nama_lengkap'] = $p->nama;
                $array['jenis_identitas_kode'] = $p->jenis_identitas_kode;
                $array['no_identitas'] = $p->no_identitas;
                $array['status_validasi'] = $cpl->status_validasi;
                $response[] = $array;
            }
        }
        return view('verifikasi_anggota_pasien_baru', ['pasien' => $response]);
    }

    public function validasiAnggotaPasienBaru(Request $request, $id)
    {
        $pasien = PasienSementara::where('id', $id)->get();
        $detail_akun = DetailAkun::where('id_pasien_temp', $id)->first();
        $agama = Agama::where('kode', $pasien[0]->agama_kode)->get();
        $pendidikan_terakhir = PendidikanTerakhir::where('kode', $pasien[0]->pendidikan_kode)->get();
        $kewarganegaraan_kode = Kewarganegaraan::where('kode', $pasien[0]->kewarganegaraan_kode)->get();
        $jenis_identitas_kode = jenis_identitas::where('kode', $pasien[0]->jenis_identitas_kode)->get();
        $jenis_kelamin = JenisKelamin::where('kode', $pasien[0]->jkel)->get();
        $status_perkawinan = StatusMenikah::where('kode', $pasien[0]->status_perkawinan)->get();
        $kedudukan_keluarga = KedudukanKeluarga::where('kode', $pasien[0]->kedudukan_keluarga)->get();
        $golongan_darah = GolonganDarah::where('kode', $pasien[0]->golongan_darah)->get();
        $provinsi = Provinsi::where('kode', $pasien[0]->provinsi)->get();
        $kabupaten = KotaKabupaten::where('kode', $pasien[0]->kabupaten)->get();
        $kecamatan = Kecamatan::where('kode', $pasien[0]->kecamatan)->get();
        $penghasilan = Penghasilan::where('kode', $pasien[0]->penghasilan)->get();
        $penanggung = Penanggung::where('id_pasien_temp', $pasien[0]->id)->get();
        $foto_pasien = FotoPasien::where('id_pasien_temp', $pasien[0]->id)->get();
        $akun = User::where('id', $detail_akun->id_akun)->get();

        // bisa null
        $jurusan = Jurusan::where('kode', $pasien[0]->jurusan)->first();
        $suku_kode = Suku::where('kode', $pasien[0]->suku_kode)->first();
        
        return view('validasi_anggota_pasien_baru', 
                    ['pasien' => $pasien, 
                    'agama' => $agama,
                    'pendidikan_terakhir' => $pendidikan_terakhir,
                    'kewarganegaraan_kode' => $kewarganegaraan_kode,
                    'jenis_identitas_kode' => $jenis_identitas_kode,
                    'suku_kode' => $suku_kode,
                    'jenis_kelamin' => $jenis_kelamin,
                    'status_perkawinan' => $status_perkawinan,
                    'kedudukan_keluarga' => $kedudukan_keluarga,
                    'golongan_darah' => $golongan_darah,
                    'provinsi' => $provinsi,
                    'kabupaten' => $kabupaten,
                    'kecamatan' => $kecamatan,
                    'jurusan' => $jurusan,
                    'penghasilan' => $penghasilan,
                    'penanggung' => $penanggung,
                    'foto_pasien' => $foto_pasien,
                    'akun' => $akun]);
    }

    public function verifikasiAnggotaPasienBaru(Request $request)
    {
        $id_akun = $request->id;
        $id_status_validasi = $request->id_status_validasi;
        $alasan_berhasil_gagal = $request->alasan_berhasil_gagal;

        // hari
        $hari = Carbon::now()->format('Y-m-d H:i:s');

        if($id_status_validasi == 1)
        {
            $title = "Verifikasi Pasien Berhasil";
            $pesan = "Data Pasien Berhasil Di Verifikasi";
        }
        else if($id_status_validasi == 2)
        {
            $title = "Verifikasi Pasien Gagal";
            $pesan = "Data Pasien Gagal Di Verifikasi";
        }

        if($id_status_validasi == 1)
        {
            // ambil data pasien temp dari id akun
            $akun = User::where('id', $id_akun)->first();
            $list_pasien = PasienSementara::where('id', $akun->id_pasien_temp)->first();

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

            // input data
            $pasien = new Pasien();
            $pasien->kode = $nomor_rekam_medik;
            $pasien->no_identitas = $list_pasien->no_identitas;
            $pasien->jenis_identitas_kode = $list_pasien->jenis_identitas_kode;
            $pasien->nama = $list_pasien->nama;
            $pasien->tempat_lahir = $list_pasien->tempat_lahir;
            $pasien->tanggal_lahir = $list_pasien->tanggal_lahir;
            $pasien->kedudukan_keluarga = $list_pasien->kedudukan_keluarga;
            $pasien->golongan_darah = $list_pasien->golongan_darah;
            $pasien->agama_kode = $list_pasien->agama_kode;
            $pasien->suku_kode = $list_pasien->suku_kode;
            $pasien->no_telp = $list_pasien->no_telp;
            $pasien->jkel = $list_pasien->jkel;
            $pasien->alamat = $list_pasien->alamat;
            $pasien->provinsi = $list_pasien->provinsi;
            $pasien->kabupaten = $list_pasien->kabupaten;
            $pasien->kecamatan = $list_pasien->kecamatan;
            $pasien->status_perkawinan = $list_pasien->status_perkawinan;
            $pasien->umur = $list_pasien->umur;
            $pasien->anak_ke = $list_pasien->anak_ke;
            $pasien->pendidikan_kode = $list_pasien->pendidikan_kode;
            $pasien->jurusan = $list_pasien->jurusan;
            $pasien->nama_tempat_bekerja = $list_pasien->nama_tempat_bekerja;
            $pasien->alamat_tempat_bekerja = $list_pasien->alamat_tempat_bekerja;
            $pasien->penghasilan = $list_pasien->penghasilan;
            $pasien->pekerjaan_kode = $list_pasien->pekerjaan_kode;
            $pasien->kewarganegaraan_kode = $list_pasien->kewarganegaraan_kode;
            $pasien->nama_pasangan = $list_pasien->nama_pasangan;
            $pasien->ayah_nama = $list_pasien->ayah_nama;
            $pasien->no_rekam_medik_ayah = $list_pasien->no_rekam_medik_ayah;
            $pasien->ibu_nama = $list_pasien->ibu_nama;
            $pasien->no_rekam_medik_ibu = $list_pasien->no_rekam_medik_ibu;
            $pasien->alergi = $list_pasien->alergi;

            // update table users atau akun
            $user = User::find($id_akun);
            $user->id_pasien_temp = null;
            $user->kode = (int)$nomor_rekam_medik;

            // cari dan update foto pasien
            $cari_foto_pasien = FotoPasien::where('id_pasien_temp', $akun->id_pasien_temp)->first();
            $foto_pasien = FotoPasien::find($cari_foto_pasien->id);
            $foto_pasien->id_pasien_temp = null;
            $foto_pasien->id_pasien = (int)$nomor_rekam_medik;

            // cari dan update penanggung
            $cari_penanggung = Penanggung::where('id_pasien_temp', $akun->id_pasien_temp)->get();
            foreach($cari_penanggung as $cp)
            {
                $penanggung = Penanggung::find($cp->id);
                $penanggung->id_pasien_temp = null;
                $penanggung->pasien_id = (int)$nomor_rekam_medik;
                $penanggung->save();
            }

            //simpan semua data
            $pasien->save();
            $user->save();
            $foto_pasien->save();

            // hapus data di tabel pasien sementara
            $hapus_pasien_sementara = PasienSementara::find($list_pasien->id);
            $hapus_pasien_sementara->delete();

            $details = [
                'title' => $title,
                'body' => $alasan_berhasil_gagal,
                'otp' => '',
                'hash_otp' => ''
            ];

            Mail::to($akun->email)->send(new MyTestMail($details));

            Notification::sendNotification($akun->firebase_token, 'Berhasil Divalidasi', 'Data Pasien Berhasil Divalidasi', null);

            // cari email
            $user_email = User::where('id', $id_akun)->first();

            //simpan data ke tb notif
            $notif = new Notif();
            $notif->email = $user_email->email;
            $notif->subjek = $title;
            $notif->isi = $alasan_berhasil_gagal;
            $notif->waktu = $hari;
            $notif->save();

            return redirect('list-pasien-baru')->with('pesan', $pesan);
            
        }
        else if($id_status_validasi == 2)
        {
            // ambil data pasien temp dari id akun
            $akun = User::where('id', $id_akun)->first();
            $list_pasien = PasienSementara::where('id', $akun->id_pasien_temp)->first();

            // update data di tabel pasien sementara
            $update_pasien_sementara = PasienSementara::find($list_pasien->id);
            $update_pasien_sementara->status_validasi = "2";
            $update_pasien_sementara->save();

            //cari foto pasien
            $foto_pasien_tb = FotoPasien::where('id_pasien_temp', $list_pasien->id)->first();
            $foto_swa = $foto_pasien_tb->foto_swa_pasien;
            $foto_kartu_pasien = $foto_pasien_tb->foto_kartu_identitas_pasien;
            //hapus data foto pasien
            $hapus_foto_pasien = FotoPasien::find($foto_pasien_tb->id);
            if(File::exists(public_path($foto_swa)) && File::exists(public_path($foto_kartu_pasien))){
                File::delete(public_path($foto_swa));
                File::delete(public_path($foto_kartu_pasien));
                $hapus_foto_pasien->delete();
            }

            //cari penanggung
            $foto_penanggung_tb = Penanggung::where('id_pasien_temp', $list_pasien->id)->get();
            foreach($foto_penanggung_tb as $fpt)
            {
                $foto_penanggung = $fpt->foto_kartu_penanggung;
                $hapus_penanggung = Penanggung::find($fpt->id);
                //hapus data penanggung
                if(File::exists(public_path($foto_penanggung))){
                    File::delete(public_path($foto_penanggung));
                    $hapus_penanggung->delete();
                }
            }

            $pesan = "Verifikasi data pasien di tolak";

            // cari email
            $user_email = User::where('id', $id_akun)->first();

            Notification::sendNotification($akun->firebase_token, 'Gagal Divalidasi', 'Data Pasien Ditolak', null);  

            //simpan data ke tb notif
            $notif = new Notif();
            $notif->email = $user_email->email;
            $notif->subjek = $title;
            $notif->isi = $alasan_berhasil_gagal;
            $notif->waktu = $hari;
            $notif->save();

            return redirect('/list-pasien-baru')->with('pesangagal', $pesan);
        }
    }
}
