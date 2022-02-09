<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Models\V1\StatusMenikah;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Exception;

class StatusMenikahController extends Controller
{
    public function getStatusMenikah(){
        try{
            $list = StatusMenikah::all();
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
