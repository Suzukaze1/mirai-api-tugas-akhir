<?php

namespace App\Http\Controllers\View;

use App\Http\Controllers\Controller;
use App\Mail\MyTestMail;
use App\Models\Admin;
use App\Models\User;
use App\Models\V1\Agama;
use App\Models\V1\FotoPasien;
use App\Models\V1\GolonganDarah;
use App\Models\V1\jenis_identitas;
use App\Models\V1\JenisKelamin;
use App\Models\V1\Jurusan;
use App\Models\V1\Kecamatan;
use App\Models\V1\KedudukanKeluarga;
use App\Models\V1\Kewarganegaraan;
use App\Models\V1\KotaKabupaten;
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

        if($id_status_validasi == 1){
            $title = "Verifikasi Pasien Berhasil";
            $pesan = "Data Pasien Berhasil Di Verifikasi";
        }else if($id_status_validasi == 2){
            $title = "Verifikasi Pasien Gagal";
            $pesan = "Data Pasien Gagal Di Verifikasi";
        }

        $akun = User::where('id', $id_akun)->first();

        $details = [
            'title' => $title,
            'body' => $alasan_berhasil_gagal,
            'otp' => '',
            'hash_otp' => ''
        ];

        Mail::to($akun->email)->send(new MyTestMail($details));
        return redirect('list-pasien')->with('pesan', $pesan);
    }
}
