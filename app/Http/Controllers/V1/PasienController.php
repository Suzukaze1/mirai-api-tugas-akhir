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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;

class PasienController extends Controller
{
    public function pendaftaranPasienBaru(Request $request){
        $cek_pasien = Pasien::latest()->first();
        $kode_rm = 1;
        if($cek_pasien == null) {
            $a = $kode_rm;
        } else {
            if ($cek_pasien->kode == null){
                $a = 1;
            }else if($cek_pasien->kode >= 1){
                $a = $cek_pasien->kode++;
            }
        }
        
        try{
            
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

            // echo $request->no_identitas;
            // die();

            Pasien::create([
                'kode' => $a,
                'agama_kode' => $request->agama_kode,
                'pendidikan_kode' => $request->pendidikan_kode,
                'pekerjaan_kode' => $request->pekerjaan_kode,
                'kewarganegaraan_kode' => $request->kewarganegaraan_kode,
                'jenis_identitas_kode' => $request->jenis_identitas_kode,
                'suku_kode' => $request->suku_kode,
                'no_identitas' => $request->no_identitas,
                'nama' => $request->nama,
                'ayah_nama' => $request->ayah_nama,
                'ibu_nama' => $request->ibu_nama,
                'nama_pasangan' => $request->nama_pasangan,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat' => $request->alamat,
                'jkel' => $request->jkel,
                'no_telp' => $request->no_telp,
                'alergi' => $request->alergi,
                'provinsi' => $request->provinsi,
                'kabupaten' => $request->kabupaten,
                'kecamatan' => $request->kecamatan,
                'umur' => $request->umur,
                'anak_ke' => $request->anak_ke,
                'jurusan' => $request->jurusan,
                'nama_tempat_bekerja' => $request->nama_tempat_bekerja,
                'alamat_tempat_bekerja' => $request->alamat_tempat_bekerja,
                'no_rekam_medik_ayah' => $request->no_rekam_medik_ayah,
                'no_rekam_medik_ibu' => $request->no_rekam_medik_ibu
            ]);

            $pasien = Pasien::where('kode', $kode_rm)->first();

            User::create([
                'name' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'kode' => $pasien->id
            ]);

            $useer = User::where('kode', $pasien->id)->first();

            // create data penanggung bagian gambar
            // create data penanggung
            $nama_foto_penannggung = Foto::simpan_foto($request, Penanggung::$FOTO_KARTU_PENANGGUNG);
            $create_penanggung = new Penanggung();
            $create_penanggung->nama_penanggung = $request->nama_penanggung;
            $create_penanggung->nomor_kartu = $request->nomor_kartu;
            $create_penanggung->pasien_id = $pasien->id;
            $create_penanggung->foto_kartu_penanggung = "/".Penanggung::$FOTO_KARTU_PENANGGUNG."/" . $nama_foto_penannggung;
            $create_penanggung->save();

            // create data foto_pasien bagian gambar swa dan identitas
            $nama_swafoto = Foto::simpan_foto($request, FotoPasien::$FOTO_SWA_PASIEN);
            $nama_kartu_identitas_foto = Foto::simpan_foto($request, FotoPasien::$FOTO_KARTU_IDENTITAS_PASIEN);

            //create data foto_pasien
            $create_foto_pasien = new FotoPasien();
            $create_foto_pasien->id_pasien = $pasien->id;
            $create_foto_pasien->foto_swa_pasien = "/".FotoPasien::$FOTO_SWA_PASIEN."/" . $nama_swafoto;
            $create_foto_pasien->foto_kartu_identitas_pasien = "/".FotoPasien::$FOTO_KARTU_IDENTITAS_PASIEN."/" . $nama_kartu_identitas_foto;
            $create_foto_pasien->save();

            //create detail akun
            $create_detail_akun = new DetailAkun();
            $create_detail_akun->id_pasien = $pasien->id;
            $create_detail_akun->id_akun = $useer->id;
            $create_detail_akun->save();

            return ResponseFormatter::success_ok(
                'user register',
                ['user' => $pasien]);
        } catch (Exception $e){
            return ResponseFormatter::error_not_found([
                'message' => 'something went wrong',
                'error' => $e
            ], 'Authentication Failed', 500);
        }
    }
}
