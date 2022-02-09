<?php

namespace App\Http\Controllers\V1;

use App\Models\V1\Suku;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Exception;

class SukuController extends Controller
{
    public function getSuku(){
        try{
            $list = Suku::orderBy('nama', 'ASC')->get();
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
