<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Models\V1\Kewarganegaraan;
use App\Http\Controllers\Controller;
use Exception;

class KewarganegaraanController extends Controller
{
    public function getKewarganegaraan(){
        try{
            $list = Kewarganegaraan::all();
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
