<?php

namespace App\Http\Controllers\V1;

use Exception;
use App\Helpers\Foto;
use App\Models\V1\Pasien;
use Illuminate\Http\Request;
use App\Models\V1\Penanggung;
use App\Models\V1\NamaPenanggung;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\V1\PasienSementara;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class PenanggungController extends Controller
{
    public function listPenanggung(Request $request)
    {
        try{
            $email = $request->input('email');

            $akun = User::where('email', $email)->first();

            if($akun->id_pasien_temp == null)
            {
                // ubah jadi int nyesuain db
                $no_rm = (int) $akun->kode;
                // cek apakah ada penanggung
                $cek = Penanggung::where('pasien_id', $no_rm)->first();
                if($cek == null) return ResponseFormatter::forbidden("Tidak Ditemukan Email Silahkan Login Ulang", null);
                // jika ada
                $penanggung = Penanggung::where('pasien_id', $no_rm)->where('nama_penanggung', "!=" ,'1')->orderBy('id', 'asc')->get();
            }
            elseif($akun->kode == null)
            {
                echo "disana";
                // ubah jadi int nyesuain db
                $no_rm = (int) $akun->id_pasien_temp;
                // cek apakah ada penanggung
                $cek = Penanggung::where('id_pasien_temp', $no_rm)->first();
                if($cek == null) return ResponseFormatter::forbidden("Tidak Ditemukan Email Silahkan Login Ulang", null);
                // jika ada
                $penanggung = Penanggung::where('id_pasien_temp', $no_rm)->where('nama_penanggung', "!=" ,'1')->orderBy('id', 'asc')->get();
            }
        
            $list_penanggung = [];
            $response = [];
            foreach($penanggung as $p)
            {
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

                $list_penanggung['id_data_penanggung'] = $p->id;
                $list_penanggung['nama_pasien'] = $nama_pasien;
                $list_penanggung['nama_penanggung'] = $nama_penanggung;
                $list_penanggung['nomor_kartu'] = $p->nomor_kartu_penanggung;
                $list_penanggung['foto_penanggung'] = $foto_penanggung;
                $list_penanggung['foto_kartu'] = $p->foto_kartu_penanggung;
                $response[] = $list_penanggung;
            }

            return ResponseFormatter::success_ok("Berhasil Mendapatkan data", $response);
        }catch (Exception $e){
            return ResponseFormatter::internal_server_error("Kesalahan Dari Server", $e);
        }
    }

    public function validasiPenanggung(Request $request)
    {
        try{
            $email = $request->input('email');

            //cek email
            $user = User::where('email', $email)->first();
            if($user == null) return ResponseFormatter::forbidden("Email Salah Silahkan Login Kembali", null);

            //jika ada
            if($user->id_pasien_temp == null)
            {
                $no_rm = $user->kode;
                $cek = Penanggung::where('pasien_id', $no_rm)->where('nama_penanggung', '!=', '1')->orderBy('nama_penanggung', 'asc')->get();
            }
            elseif($user->kode == null)
            {
                $no_rm = $user->id_pasien_temp;
                $cek = Penanggung::where('id_pasien_temp', $no_rm)->where('nama_penanggung', '!=', '1')->orderBy('nama_penanggung', 'asc')->get();
            }
            
            if(count($cek) == 0) return ResponseFormatter::forbidden("Data Tidak Ditemukan Silahkan Login Ulang", null);
            $penanggung = [];
            $n_pen = [];
            $response = [];
            foreach($cek as $c)
            {
                $penanggung[] = $c->nama_penanggung;
            }

            //validasi 
            if($penanggung[0] == "2" && empty($penanggung[1]) && empty($penanggung[2]))
            {
                $nam_pen = NamaPenanggung::where('kode', '3')->orWhere('kode', '4')->get();
                foreach($nam_pen as $np){
                    $n_pen['id_penanggung'] = $np->kode;
                    $n_pen['nama_penanggung'] = $np->nama;
                    $response[] = $n_pen;
                }
                return ResponseFormatter::success_ok("Berhasil Mendapatkan Data", $response);
            }
            elseif ($penanggung[0] == "3" && empty($penanggung[1]) && empty($penanggung[2]))
            {
                $nam_pen = NamaPenanggung::where('kode', '2')->orWhere('kode', '4')->get();
                foreach($nam_pen as $np){
                    $n_pen['id_penanggung'] = $np->kode;
                    $n_pen['nama_penanggung'] = $np->nama;
                    $response[] = $n_pen;
                }
                return ResponseFormatter::success_ok("Berhasil Mendapatkan Data", $response);
            }
            elseif ($penanggung[0] == "4" && empty($penanggung[1]) && empty($penanggung[2]))
            {
                $nam_pen = NamaPenanggung::where('kode', '2')->orWhere('kode', '3')->get();
                foreach($nam_pen as $np){
                    $n_pen['id_penanggung'] = $np->kode;
                    $n_pen['nama_penanggung'] = $np->nama;
                    $response[] = $n_pen;
                }
                return ResponseFormatter::success_ok("Berhasil Mendapatkan Data", $response);
            }
            elseif ($penanggung[0] == "2" && $penanggung[1] == "3" && empty($penanggung[2]))
            {
                $nam_pen = NamaPenanggung::where('kode', '4')->get();
                foreach($nam_pen as $np){
                    $n_pen['id_penanggung'] = $np->kode;
                    $n_pen['nama_penanggung'] = $np->nama;
                    $response[] = $n_pen;
                }
                return ResponseFormatter::success_ok("Berhasil Mendapatkan Data", $response);
            }
            elseif ($penanggung[0] == "2" && $penanggung[1] == "4" && empty($penanggung[2]))
            {
                $nam_pen = NamaPenanggung::where('kode', '3')->get();
                foreach($nam_pen as $np){
                    $n_pen['id_penanggung'] = $np->kode;
                    $n_pen['nama_penanggung'] = $np->nama;
                    $response[] = $n_pen;
                }
                return ResponseFormatter::success_ok("Berhasil Mendapatkan Data", $response);
            }
            elseif ($penanggung[0] == "3" && $penanggung[1] == "4" && empty($penanggung[2]))
            {
                $nam_pen = NamaPenanggung::where('kode', '2')->get();
                foreach($nam_pen as $np){
                    $n_pen['id_penanggung'] = $np->kode;
                    $n_pen['nama_penanggung'] = $np->nama;
                    $response[] = $n_pen;
                }
                return ResponseFormatter::success_ok("Berhasil Mendapatkan Data", $response);
            }
            elseif ($penanggung[0] == "2" && $penanggung[1] == "3" && $penanggung[2] == "4")
            {
                return ResponseFormatter::error_not_found("Data Penanggung Sudah Penuh", null);
            }
            else
            {
                $nam_pen = NamaPenanggung::where('kode', '2')->orWhere('kode', '3')->orWhere('kode', '4')->get();
                foreach($nam_pen as $np){
                    $n_pen['id_penanggung'] = $np->kode;
                    $n_pen['nama_penanggung'] = $np->nama;
                    $response[] = $n_pen;
                }
                return ResponseFormatter::success_ok("Berhasil Mendapatkan Data", $nam_pen);
            }
        }catch (Exception $e){
            return ResponseFormatter::internal_server_error("Kesalahan Dari Server", $e);
        }
    }

    public function tambahPenanggung(Request $request)
    {
        $nama_penanggung = $request->nama_penanggung;
        $nomor_kartu_penanggung = $request->nomor_kartu_penanggung;
        $foto_kartu_penanggung = $request->foto_kartu_penanggung;
        $email = $request->email;

        $get_email = User::where('email', $email)->first();
        if($get_email == null) return ResponseFormatter::forbidden("Tidak Ditemukan Email Silahkan Login Ulang", null);

        if($get_email->id_pasien_temp == null)
        {
            $pasien_id = sprintf("%08s", strval($get_email->kode));
            $get_pasien = Pasien::where('kode', $pasien_id)->first();
            $nama_lengkap = $get_pasien->nama;
        }
        elseif($get_email->kode == null)
        {
            $pasien_id = $get_email->id_pasien_temp;
            $get_pasien = PasienSementara::where('id', $pasien_id)->first();
            $nama_lengkap = $get_pasien->nama;
        }

        // path gambar
        $path = Penanggung::$FOTO_KARTU_PENANGGUNG;
        $key = $foto_kartu_penanggung;
        $file = Foto::base_64_foto($path, $key, $nama_lengkap);

        //get nama penanggung
        $nama_pen = NamaPenanggung::where('kode', $nama_penanggung)->first();
        $namapenanggung = $nama_pen->nama;

        // response
        $response = [];
        $response['nama_penanggung'] = $namapenanggung;
        $response['nomor_kartu_penanggung'] = $nomor_kartu_penanggung;
        $response['foto_kartu_penanggung'] = $file;
        $response['email'] = $email;

        $tambah_penanggung = new Penanggung();
        $tambah_penanggung->nama_penanggung = $nama_penanggung;
        $tambah_penanggung->nomor_kartu_penanggung = $nomor_kartu_penanggung;
        if($get_email->id_pasien_temp == null)
        {
            $tambah_penanggung->pasien_id = (int)$pasien_id;
        }
        elseif($get_email->kode == null)
        {
            $tambah_penanggung->id_pasien_temp = (int)$pasien_id;
        }
        $tambah_penanggung->foto_kartu_penanggung = $file;
        $tambah_penanggung->save();

        return ResponseFormatter::success_ok("Berhasil Mendaftar Penanggung", $response);
        // try{
            
        // }catch (Exception $e){
        //     return ResponseFormatter::error_not_found("Kesalahan dari Server", $e);
        // }
    }

    public function hapusPenanggung(Request $request)
    {
        try{
            $id = $request->id_penanggung;

            $hapus_penanggung = Penanggung::find($id);
            $image = $hapus_penanggung->foto_kartu_penanggung;

            if(File::exists(public_path($image))){
                File::delete(public_path($image));
                $hapus_penanggung->delete();
                return ResponseFormatter::success_ok("Berhasil Mengahapus Penanggung", null);
            }else{
                return ResponseFormatter::error_not_found("Foto Penanggung Tidak Ada", null);
            }
        }catch (Exception $e){
            return ResponseFormatter::internal_server_error("Kesalahan Dari Server", $e);
        }
        
        
    }
}
