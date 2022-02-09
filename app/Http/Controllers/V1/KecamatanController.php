<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\V1\Kecamatan;
use Exception;

class KecamatanController extends Controller
{
    public function getKecamatan(Request $request){
        $id_provinsi = $request->id_provinsi;
        $id_kabupaten_kota = $request->id_kecamatan_kota;
        try{
            $list = Kecamatan::where('kode_prov', $id_provinsi)->where('kode_prov_kab', $id_kabupaten_kota)->orderBy('nama', 'ASC')->get();
            return ResponseFormatter::success_ok(
                'Berhasil Mendapatkan Data',
                $list
            );

        }catch (Exception $e){
            return ResponseFormatter::internal_server_error(
                'Ada yang Error Dari Server',
                $e
            );
        }
        
    }
}
