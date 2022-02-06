<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Pasien;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class PasienController extends Controller
{
    public function pendaftaranPasienBaru(Request $request){
        try{
            $request->validate([
                'kode' => ['required', 'string', 'max:10', 'unique:pasien'],
                'agama_kode' => ['required', 'string', 'max:10'],
                'pendidikan_kode' => ['required', 'string', 'max:10'],
                'pekerjaan_kode' => ['required', 'string', 'max:10'],
                'kewarganegaraan_kode' => ['required', 'string', 'max:10'],
                'jenis_identitas_kode' => ['required', 'string', 'max:10'],
                'suku_kode' => ['required', 'string', 'max:10'],
                'no_identitas' => ['required', 'string', 'max:10', 'unique:users'],
                'nama' => ['nullable', 'string', 'max:150'],
                'ayah_nama' => ['nullable', 'string', 'max:150'],
                'ibu_nama' => ['nullable', 'string', 'max:150'],
                'nama_pasangan' => ['nullable', 'string', 'max:150'],
                'tempat_lahir' => ['required', 'string', 'max:255'],
                'tanggal_lahir' => ['required'],
                'alamat' => ['required', 'string'],
                'jkel' => ['required', 'string', 'max:11'],
                'no_telp' => ['nullable', 'string', 'max:50'],
                'alergi' => ['nullable'],
            ]);

            Pasien::create([
                'kode' => $request->kode,
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
                'alergi' => $request->alergi
            ]);

            $pasien = Pasien::where('kode', $request->kode)->first();

            return ResponseFormatter::success_ok([
                'user' => $pasien
            ], 'User Registered');
        }catch (Exception $e){
            return ResponseFormatter::error_not_found([
                'message' => 'something went wrong',
                'error' => $e
            ], 'Authentication Failed', 500);
        }
    }
}
