<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Models\V1\GolonganDarah;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Exception;

class GolonganDarahController extends Controller
{
    public function getGolonganDarah(){
        try{
            $list = GolonganDarah::all();
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
