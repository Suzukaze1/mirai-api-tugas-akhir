<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use PhpOption\Option;

class Foto
{
    public static function simpan_foto(Request $request, $key)
    {
        $files = $request->file($key);
        $nama = $key . "_" . $request->nama . "_" . $files->getClientOriginalName();
        $tujuan_upload = $key;
        $files->move($tujuan_upload, $nama);
        return $nama;
    }

    public static function simpan_foto_ganda($request, $key, $nama)
    {

        $destination_path = getcwd() . DIRECTORY_SEPARATOR;

        $files[] = [
            'file' => $request[$key]->getRealPath(),
            'options' => [
                'mime' => $request[$key]->getClientMimeType(),
                'as'    => $request[$key]->getClientOriginalName()
            ],
        ];

        $unique = uniqid('', true);
        $unik = substr($unique, strlen($unique) - 4, strlen($unique));
        $ekstensiGambar = $files[0]['options']['as'];

        $ekstensiGambar = explode('.', $ekstensiGambar);
        $namaCustom = $unik . "." . end($ekstensiGambar);
        $nama_foto = $nama . "-" . basename($namaCustom);

        $target_path = $destination_path . "/" . $key . "/" . $nama_foto;

        move_uploaded_file($files[0]['file'], $target_path);
        return $nama_foto;
    }
}
