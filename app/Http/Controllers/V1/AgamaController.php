<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\V1\Agama;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Exception;

class AgamaController extends Controller
{
    public function getAgama(Request $request){
        try{
            $list = Agama::orderBy('id', 'ASC')->get();
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
