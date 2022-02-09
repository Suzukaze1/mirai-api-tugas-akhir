<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\V1\KedudukanKeluarga;
use Exception;

class KedudukanKeluargaController extends Controller
{
    public function getKedudukanKeluarga(){
        try{
            $list = KedudukanKeluarga::all();
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
