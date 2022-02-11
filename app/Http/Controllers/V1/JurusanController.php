<?php

namespace App\Http\Controllers\V1;

use App\Models\V1\Jurusan;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Exception;

class JurusanController extends Controller
{
    public function getJurusan(){
        try{
            $list = Jurusan::orderBy('kode', 'ASC')->get();
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
