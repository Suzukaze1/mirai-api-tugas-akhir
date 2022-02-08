<?php

namespace App\Http\Controllers\V1;

use App\Helpers\Foto;
use App\Helpers\ResponseFormatter;
use Illuminate\Http\Request;
use App\Models\V1\Penanggung;
use App\Http\Controllers\Controller;

class UploadGambarController extends Controller
{
    public function trollGambar(Request $request){
        // create data penanggung bagian gambar
        $a = Foto::simpan_foto($request, Penanggung::$FOTO_KARTU_PENANGGUNG);
        return ResponseFormatter::success_ok('Berhasil Ambil Gambar dan letak di folder', null);
    }
}
