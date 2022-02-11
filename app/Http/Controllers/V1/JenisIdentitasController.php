<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Models\V1\jenis_identitas;
use App\Http\Controllers\Controller;
use Exception;

class JenisIdentitasController extends Controller
{
    public function getJenisIdentitas(Request $request){
        try{
            $list = jenis_identitas::orderBy('kode', 'ASC')->get();
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
