<?php

namespace App\Http\Controllers\V1;

use Exception;
use Illuminate\Http\Request;
use App\Models\V1\KotaKabupaten;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;

class KotaKabupatenController extends Controller
{
    public function getKabupatenKota(Request $request){
        $kode_prov = $request->input('kode_prov');
        try{
            $list = KotaKabupaten::where('kode_prov', $kode_prov)->get();
            
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
