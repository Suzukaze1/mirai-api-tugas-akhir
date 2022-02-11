<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Models\V1\JenisKelamin;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Exception;

class JenisKelaminController extends Controller
{
    public function getJenisKelamin(Request $request){
        try{
            $list = JenisKelamin::orderBy('kode', 'ASC')->get();
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
