<?php

namespace App\Http\Controllers\View;

use App\Http\Controllers\Controller;
use App\Mail\MyTestMail;
use App\Models\Admin;
use App\Models\User;
use App\Models\V1\Agama;
use App\Models\V1\DetailAkun;
use App\Models\V1\FotoPasien;
use App\Models\V1\GolonganDarah;
use App\Models\V1\jenis_identitas;
use App\Models\V1\JenisKelamin;
use App\Models\V1\Jurusan;
use App\Models\V1\Kecamatan;
use App\Models\V1\KedudukanKeluarga;
use App\Models\V1\Kewarganegaraan;
use App\Models\V1\KotaKabupaten;
use App\Models\V1\Notif;
use App\Models\V1\Pasien;
use App\Models\V1\PasienSementara;
use App\Models\V1\Penanggung;
use App\Models\V1\PendidikanTerakhir;
use App\Models\V1\Penghasilan;
use App\Models\V1\Provinsi;
use App\Models\V1\StatusMenikah;
use App\Models\V1\Suku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;

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
        $suku_kode = Suku::where('kode', $pasien[0]->suku_kode)->get();
        $jenis_kelamin = JenisKelamin::where('kode', $pasien[0]->jkel)->get();
        $status_perkawinan = StatusMenikah::where('kode', $pasien[0]->status_perkawinan)->get();
        $kedudukan_keluarga = KedudukanKeluarga::where('kode', $pasien[0]->kedudukan_keluarga)->get();
        $golongan_darah = GolonganDarah::where('kode', $pasien[0]->golongan_darah)->get();
        $provinsi = Provinsi::where('kode', $pasien[0]->provinsi)->get();
        $kabupaten = KotaKabupaten::where('kode', $pasien[0]->kabupaten)->get();
        $kecamatan = Kecamatan::where('kode', $pasien[0]->kecamatan)->get();
        $jurusan = Jurusan::where('kode', $pasien[0]->jurusan)->get();
        $penghasilan = Penghasilan::where('kode', $pasien[0]->penghasilan)->get();
        $penanggung = Penanggung::where('id_pasien_temp', $pasien[0]->id)->get();
        $foto_pasien = FotoPasien::where('id_pasien_temp', $pasien[0]->id)->get();
        $akun = User::where('id_pasien_temp', $pasien[0]->id)->get();
        
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
            $pasien->save();

            // update table users atau akun
            $user = User::find($id_akun);
            $user->id_pasien_temp = null;
            $user->kode = (int)$nomor_rekam_medik;
            $user->save();

            // cari dan update detail akun 
            $cari_detail_akun = DetailAkun::where('id_pasien_temp', $akun->id_pasien_temp)->first();
            $detail_akun = DetailAkun::find($cari_detail_akun->id);
            $detail_akun->id_pasien_temp = null;
            $detail_akun->id_pasien = (int)$nomor_rekam_medik;
            $detail_akun->save();

            // cari dan update penanggung
            $cari_penanggung = Penanggung::where('id_pasien_temp', $akun->id_pasien_temp)->first();
            $penanggung = Penanggung::find($cari_penanggung->id);
            $penanggung->id_pasien_temp = null;
            $penanggung->pasien_id = (int)$nomor_rekam_medik;
            $penanggung->save();

            // cari dan update foto pasien
            $cari_foto_pasien = FotoPasien::where('id_pasien_temp', $akun->id_pasien_temp)->first();
            $foto_pasien = FotoPasien::find($cari_foto_pasien->id);
            $foto_pasien->id_pasien_temp = null;
            $foto_pasien->id_pasien = (int)$nomor_rekam_medik;
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

            //simpan data ke tb notif
            $notif = new Notif();
            $notif->email = $user_email->email;
            $notif->subjek = $title;
            $notif->isi = $alasan_berhasil_gagal;
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

            $pesan = "Verifikasi data pasien di tolak";

            // cari email
            $user_email = User::where('id', $id_akun)->first();

            //simpan data ke tb notif
            $notif = new Notif();
            $notif->email = $user_email->email;
            $notif->subjek = $title;
            $notif->isi = $alasan_berhasil_gagal;
            $notif->save();

            return redirect('/list-pasien-baru')->with('pesangagal', $pesan);
        }
    }

    public function listPasienLama()
    {
        $cek_pasien_lama = DetailAkun::where('is_lama', '1')->get();

        $response = [];

        foreach($cek_pasien_lama as $cpl){
            $pasien = Pasien::where('kode', sprintf("%08s", strval($cpl->id_pasien)))->first();
            $response[] = $pasien;
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
        $suku_kode = Suku::where('kode', $pasien[0]->suku_kode)->get();
        $jenis_kelamin = JenisKelamin::where('kode', $pasien[0]->jkel)->get();
        $status_perkawinan = StatusMenikah::where('kode', $pasien[0]->status_perkawinan)->get();
        $kedudukan_keluarga = KedudukanKeluarga::where('kode', $pasien[0]->kedudukan_keluarga)->get();
        $golongan_darah = GolonganDarah::where('kode', $pasien[0]->golongan_darah)->get();
        $provinsi = Provinsi::where('kode', $pasien[0]->provinsi)->get();
        $kabupaten = KotaKabupaten::where('kode', $pasien[0]->kabupaten)->get();
        $kecamatan = Kecamatan::where('kode', $pasien[0]->kecamatan)->get();
        $jurusan = Jurusan::where('kode', $pasien[0]->jurusan)->get();
        $penghasilan = Penghasilan::where('kode', $pasien[0]->penghasilan)->get();
        $penanggung = Penanggung::where('pasien_id', $pasien[0]->id)->get();
        $foto_pasien = FotoPasien::where('id_pasien', $pasien[0]->id)->get();
        $akun = User::where('kode', $pasien[0]->id)->get();
        
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
}
