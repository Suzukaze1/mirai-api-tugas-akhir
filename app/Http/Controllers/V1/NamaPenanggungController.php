<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Models\V1\NamaPenanggung;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Exception;

class NamaPenanggungController extends Controller
{
    public function getNamaPenanggung(){
        try{
            $list = NamaPenanggung::orderBy('kode', 'asc')->get();
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
