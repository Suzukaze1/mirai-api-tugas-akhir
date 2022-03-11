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
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class PenanggungController extends Controller
{
    public function listPenanggung(Request $request)
    {
        try{
            $nomor_rm = $request->input('nomor_rekam_medis');

            // ubah jadi int nyesuain db
            $no_rm = (int) $nomor_rm;

            // cek apakah ada penanggung
            $cek = Penanggung::where('pasien_id', $no_rm)->first();
            if($cek == null) return ResponseFormatter::forbidden("Tidak Ditemukan Nomor Rekam Medis Silahkan Login Ulang", null);

            // jika ada
            $penanggung = Penanggung::where('pasien_id', $no_rm)->get();
            $list_penanggung = [];
            $response = [];
            foreach($penanggung as $p)
            {
                $nam_pen = NamaPenanggung::where('kode', $p->nama_penanggung)->first();
                $nama_penanggung = $nam_pen->nama;
                $list_penanggung['id_penanggung'] = $p->id;
                $list_penanggung['nama_penanggung'] = $nama_penanggung;
                $list_penanggung['nomor_kartu'] = $p->nomor_kartu_penanggung;
                //$list_penanggung['foto_penanggung'] = $p->foto_kartu_penanggung;
                $response[] = $list_penanggung;
            }

            return ResponseFormatter::success_ok("Berhasil Mendapatkan data", $response);
        }catch (Exception $e){
            return ResponseFormatter::internal_server_error("Kesalahan Dari Server", $e);
        }
    }

    public function validasiPenanggung(Request $request)
    {
        $no_rm = (int)$request->input('nomor_rekam_medis');
        $cek = Penanggung::where('pasien_id', $no_rm)->get();
        $penanggung = [];
        $response = [];
        foreach($cek as $c)
        {
            $penanggung[] = $c->nama_penanggung;
        }

        $a = array_search("2",$penanggung);
        if($a){
            return "KIS dan JAMKESDA";
        }
        die();

        // validasi
        if($penanggung[0] == "2" && $penanggung[1] == "3" && $penanggung[2] == "4")
        {
            return ResponseFormatter::error_not_found("Data Penanggung Sudah Penuh", null);
        }
        elseif ($penanggung[0] == "2" && $penanggung[1] == "3")
        {
            $nam_pen = NamaPenanggung::where('kode', '3')->get();
            return ResponseFormatter::success_ok("Berhasil Mendapatkan Data", $nam_pen);
        }
        elseif ($penanggung[0] == "2" && $penanggung[2] == "4")
        {
            $nam_pen = NamaPenanggung::where('kode', '2')->get();
            return ResponseFormatter::success_ok("Berhasil Mendapatkan Data", $nam_pen);
        }
        elseif ($penanggung[1] == "3" && $penanggung[2] == "4")
        {
            $nam_pen = NamaPenanggung::where('kode', '1')->get();
            return ResponseFormatter::success_ok("Berhasil Mendapatkan Data", $nam_pen);
        }
        elseif ($penanggung[2] == "4")
        {
            $nam_pen = NamaPenanggung::where('kode', '1')->oWhere('kode', '2')->get();
            return ResponseFormatter::success_ok("Berhasil Mendapatkan Data", $nam_pen);
        }
        elseif ($penanggung[1] == "3")
        {
            $nam_pen = NamaPenanggung::where('kode', '1')->oWhere('kode', '3')->get();
            return ResponseFormatter::success_ok("Berhasil Mendapatkan Data", $nam_pen);
        }
        elseif ($penanggung[0] == "2")
        {
            $nam_pen = NamaPenanggung::where('kode', '2')->oWhere('kode', '3')->get();
            return ResponseFormatter::success_ok("Berhasil Mendapatkan Data", $nam_pen);
        }
        else{
            $nam_pen = NamaPenanggung::all();
            return ResponseFormatter::success_ok("Berhasil Mendapatkan Data", $nam_pen);
        }
        // try{
            
        // }catch(Exception $e){
        //     return ResponseFormatter::internal_server_error("Kesalahan Dari Server", $e);
        // }
    }

    public function tambahPenanggung(Request $request)
    {
        try{
            $nama_penanggung = $request->nama_penanggung;
            $nomor_kartu_penanggung = $request->nomor_kartu_penanggung;
            $foto_kartu_penanggung = $request->foto_kartu_penanggung;
            $pasien_id = $request->nomor_rekam_medis;

            $get_pasien = Pasien::where('kode', $pasien_id)->first();
            $nama_lengkap = $get_pasien->nama;

            // path gambar
            $path = Penanggung::$FOTO_KARTU_PENANGGUNG;
            $key = $foto_kartu_penanggung;
            $file = Foto::base_64_foto($path, $key, $nama_lengkap);

            // response
            $response = [];
            $response['nama_penanggung'] = $nama_penanggung;
            $response['nomor_kartu_penanggung'] = $nomor_kartu_penanggung;
            $response['nomor_rekam_medik'] = (int)$nama_penanggung;
            $response['nama_penanggung'] = $nama_penanggung;

            $tambah_penanggung = new Penanggung();
            $tambah_penanggung->nama_penanggung = $nama_penanggung;
            $tambah_penanggung->nomor_kartu_penanggung = $nomor_kartu_penanggung;
            $tambah_penanggung->pasien_id = $pasien_id;
            $tambah_penanggung->foto_kartu_penanggung = $file;
            $tambah_penanggung->save();

            return ResponseFormatter::success_ok("Berhasil Mendaftar Penanggung", $response);
        }catch (Exception $e){
            return ResponseFormatter::error_not_found("Kesalahan dari Server", $e);
        }
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
