<?php

namespace App\Http\Controllers\V1;

use App\Helpers\Constant;
use App\Helpers\Foto;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\V1\DetailAkun;
use App\Models\V1\FotoPasien;
use App\Models\V1\Pasien;
use App\Models\V1\Penanggung;
use App\Models\User;
use Exception;
use Facade\FlareClient\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;

class PasienController extends Controller
{
    public function pendaftaranPasienBaru(Request $request){
        // logika nomor rekam medik
        $cek_pasien = Pasien::latest()->first();
        
        $kode_rm = 0;
        if($cek_pasien == null) {
            $kode_rm = 1;
        } else {
            if ($cek_pasien->kode == null){
                $kode_rm = 1;
            }else if($cek_pasien->kode >= 1){
                $kode_rm = (int)$cek_pasien->kode + 1; 
            }
        }

        // nomor rekam medik
        $nomor_rekam_medik = sprintf("%08s", strval($kode_rm));
        
        // $request->validate([
            //     'kode' => ['required', 'string', 'max:10', 'unique:pasien'],
            //     'agama_kode' => ['required', 'string', 'max:10'],
            //     'pendidikan_kode' => ['required', 'string', 'max:10'],
            //     'pekerjaan_kode' => ['required', 'string', 'max:10'],
            //     'kewarganegaraan_kode' => ['required', 'string', 'max:10'],
            //     'jenis_identitas_kode' => ['required', 'string', 'max:10'],
            //     'suku_kode' => ['required', 'string', 'max:10'],
            //     'no_identitas' => ['required', 'string', 'max:10', 'unique:users'],
            //     'nama' => ['nullable', 'string', 'max:150'],
            //     'ayah_nama' => ['nullable', 'string', 'max:150'],
            //     'ibu_nama' => ['nullable', 'string', 'max:150'],
            //     'nama_pasangan' => ['nullable', 'string', 'max:150'],
            //     'tempat_lahir' => ['required', 'string', 'max:255'],
            //     'tanggal_lahir' => ['required'],
            //     'alamat' => ['required', 'string'],
            //     'jkel' => ['required', 'string', 'max:11'],
            //     'no_telp' => ['nullable', 'string', 'max:50'],
            //     'alergi' => ['nullable'],
            //     'kedudukan_keluarga' => ['nullable'],
            //     'golongan_darah' => ['nullable'],
            //     'provinsi' => ['required'],
            //     'kabupaten' => ['required'],
            //     'kecamatan' => ['required'],
            //     'umur' => ['nullable'],
            //     'anak_ke' => ['nullable'],
            //     'jurusan' => ['nullable'],
            //     'nama_tempat_bekerja' => ['required'],
            //     'alamat_tempat_bekerja' => ['required'],
            //     'no_rekam_medik_ayah' => ['nullable'],
            //     'no_rekam_medik_ibu' => ['nullable'],

            //     'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            //     'password' => ['required', 'string'],

            //     'nama_penanggung' => ['required'],
            //     'nomor_kartu' => ['required'],
            // ]);

        // get value text
        $nomor_identitas = $request->nomor_identitas;
        $jenis_identitas = $request->jenis_identitas;
        $nama_lengkap = $request->nama_lengkap;
        $tempat_lahir = $request->tempat_lahir;
        $tanggal_lahir = $request->tanggal_lahir;
        $kedudukan_keluarga = $request->kedudukan_keluarga;
        $golongan_darah = $request->golongan_darah;
        $agama= $request->agama;
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

        // $daftar_penanggung = $request->daftar_penanggung;
        $nama_penanggung = $request->nama_penanggung;
        $nomor_kartu_penanggung = $request->nomor_kartu_penanggung;

        $email = $request->email;
        $password = $request->password;

        //get value file
        

        // buat data di tb pasien
        try{
            $pasien = new Pasien();
            $pasien->kode = $nomor_rekam_medik;
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

            return ResponseFormatter::success_ok('Berhasil Membuat Penanggung', $$pasien);
            die();

            //$pasien->save();
        }catch (Exception $e){
            return ResponseFormatter::internal_server_error('Ada Yang Error Dari Server (pasien)', $pasien);
        }

        // cari data pasien yang sudah dibuat tadi
        $cari_pasien = Pasien::where('kode', $kode_rm)->first();

        // buat data di tb users
        try{
            $akun = new User();
            $akun->nama = $nama_lengkap;
            $akun->email = $email;
            $akun->password = Hash::make($password);
            $akun->kode = $cari_pasien->id;

            $akun->save();
        }catch (Exception $e){
            return ResponseFormatter::internal_server_error('Ada Yang Error Dari Server (users)', $e);
        }

        // cari data users yang sudah dibuat tadi
        $cari_akun = User::where('kode', $cari_pasien->id)->first();

        // buat data di tb detail_akun
        try{
            $detail_akun = new DetailAkun();
            $detail_akun->id_pasien = $cari_pasien->id;
            $detail_akun->id_akun = $cari_akun->id;
            $detail_akun->save();
        }catch (Exception $e){
            return ResponseFormatter::internal_server_error('Ada Yang Error Dari Server (detail_akun)', $e);
        }

        // buat data di tb foto_pasien
        try{
            $foto_pasien = new FotoPasien();
            $nama_swafoto = Foto::simpan_foto($request, FotoPasien::$FOTO_SWA_PASIEN);
            $nama_kartu_identitas_foto = Foto::simpan_foto($request, FotoPasien::$FOTO_KARTU_IDENTITAS_PASIEN);
            $foto_pasien->id_pasien = $cari_pasien->id;
            $foto_pasien->swafoto = "/".FotoPasien::$FOTO_SWA_PASIEN."/" . $nama_swafoto;
            $foto_pasien->foto_identitas = "/".FotoPasien::$FOTO_KARTU_IDENTITAS_PASIEN."/" . $nama_kartu_identitas_foto;
            $foto_pasien->save();
        }catch (Exception $e){
            return ResponseFormatter::internal_server_error('Ada Yang Error Dari Server (foto_pasien)', $e);
        }

        //buat data di tb penanggung
        try{
            $list_penanggung = array();
            foreach ($request->daftar_penanggung as $penanggungs){
                $nama_foto_penanggung = Foto::simpan_foto_ganda($penanggungs, Penanggung::$FOTO_KARTU_PENANGGUNG, $nama_lengkap);
                $path_foto = "/" . Penanggung::$FOTO_KARTU_PENANGGUNG ."/" . $nama_foto_penanggung;
                $penanggung = new Penanggung();
                $penanggung->nama_penanggung = $penanggungs['nama_penanggung'];
                $penanggung->nomor_kartu = $penanggungs['nomor_kartu_penanggung'];
                $penanggung->pasien_id = $cari_pasien->id;
                $penanggung->foto_kartu_penanggung = $path_foto;
                // $gambar_string = $penanggungs['foto_kartu_penanggung'];
                // $gambar_asli = base64_decode($gambar_string);
                // file_put_contents()
                // $penanggung->foto_kartu_penanggung = $penanggungs['foto_kartu_penanggung'];
                // return $daftar_penanggung;
                // die();
                $penanggung->save();
                $list_penanggung[] = $penanggung;
            }
            // return ResponseFormatter::success_ok('Berhasil Membuat Penanggung', $list_penanggung);
        }catch (Exception $e){
            return ResponseFormatter::internal_server_error('Ada Yang Error Dari Server(penangggung)', 
            [$list_penanggung,
            $e
            ]);
        }
        
    }

    public function pendaftaranPasienLama(Request $request){
        // get value text
        $no_rekam_medik = $request->kode;
        $tgl_lahir = $request->tanggal_lahir;
        $jenis_identitas = $request->jenis_identitas_kode;
        $nomor_identitas = $request->nomor_identitas;
        $email = $request->email;
        $password = $request->password;
        $ulang_password = $request->ulang_password;

        // get value file
        $nama_swafoto = Foto::simpan_foto($request, FotoPasien::$FOTO_SWA_PASIEN);
        $nama_kartu_identitas_foto = Foto::simpan_foto($request, FotoPasien::$FOTO_KARTU_IDENTITAS_PASIEN);

        // cek ke db pasien
        $pasien = Pasien::where('kode', $no_rekam_medik)->first();

        // cek apakah data pasien null atau tidak
        if($pasien == null){
            return "Data Pasien Tidak Ada Amda Jangan Macam2 Daftar Pulak";
        }else{
            //cek ke db user apakah pasien sudah mendaftar sebelumnya
            $user = User::where('kode', $pasien->id)->first();
            if($user != null){
                //jika null atau tidak ada data maka lanjut ke step selanjutnya
                if($pasien->id == $user->kode){
                    //jika sudah ada data maka berhenti disni
                    return "Sudah Terdafatar";
                }
            }
        }

        // logika seluruh validasi tidak termasuk angka/text/strinf dll
        if ($pasien == null) return "Kode Rm Tidak TErdaftar";

        if ($pasien->tanggal_lahir != $tgl_lahir) return "Tanggal Lahir Tak betul la";

        if($pasien->jenis_identitas_kode != $jenis_identitas) return "Jenis Identitas Salah Goblok";
  
        if($pasien->no_identitas != $nomor_identitas) return "Nomor Identitas Salah";

        if($password != $ulang_password) return "Password Tidak Sama";

        // buat data di table users
        $create_users = new User();
        $create_users->name = $pasien->nama;
        $create_users->email = $email;
        $create_users->password = Hash::make($password);
        $create_users->kode = $pasien->id;

        // buat data di table foto pasien
        $create_foto_pasien= new FotoPasien();
        $create_foto_pasien->id_pasien = $pasien->id;
        $create_foto_pasien->foto_swa_pasien = "/".FotoPasien::$FOTO_SWA_PASIEN."/" . $nama_swafoto;
        $create_foto_pasien->foto_kartu_identitas_pasien = "/".FotoPasien::$FOTO_KARTU_IDENTITAS_PASIEN."/" . $nama_kartu_identitas_foto;

        try{
            //jika berhasil
            $create_users->save();
            $create_foto_pasien->save();
            return ResponseFormatter::success_ok('Berhasil Mendaftar Akun', null);
        }catch (Exception $e){
            //jika gagal
            return ResponseFormatter::internal_server_error('Ada Sesuatu Yang salah', $e);
        }
        
    }
}
