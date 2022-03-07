<?php

namespace App\Http\Controllers\V1;

use App\Dummy\DataDummy;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Exception;

class BantuanController extends Controller
{
    public function getBantuan()
    {
        $dumBantuan =  DataDummy::dummyBantuan();
        try{
            return ResponseFormatter::success_ok(
                'Berhasil Mendapatkan Data',
                $dumBantuan
            );

        }catch (Exception $e){
            return ResponseFormatter::internal_server_error(
                'Ada yang Error Dari Server',
                $e
            );
        }
    }
}
