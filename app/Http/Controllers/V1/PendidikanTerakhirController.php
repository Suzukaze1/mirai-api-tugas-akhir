<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\V1\PendidikanTerakhir;
use Exception;

class PendidikanTerakhirController extends Controller
{
    public function getPendidikanTerakhir(){
        try{
            $list = PendidikanTerakhir::all();
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
