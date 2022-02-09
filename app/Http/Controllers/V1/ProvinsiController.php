<?php

namespace App\Http\Controllers\V1;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\V1\Provinsi;
use Exception;
use Illuminate\Http\Request;

class ProvinsiController extends Controller
{
    public function getAllProvinsi(){
        try{
            $list = Provinsi::orderBy('nama', 'ASC')->get();
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
