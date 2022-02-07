<?php

namespace App\Helpers;

use Illuminate\Http\Request;

class Foto{
    public static function simpan_foto(Request $request, $nama_foto){
        $files = $request->file($nama_foto);
        $nama = $nama_foto."_".$request->nama."_".$files->getClientOriginalName();
        $tujuan_upload = $nama_foto;
        $files->move($tujuan_upload,$nama);
        return $nama;
    }
}