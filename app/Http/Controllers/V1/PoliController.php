<?php

namespace App\Http\Controllers\V1;

use App\Dummy\DataDummy;
use Exception;
use App\Models\V1\Poli;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;

class PoliController extends Controller
{
    public function getPoli()
    {
        $poli = DataDummy::dummyPoli();
        try{
            //$list = Poli::orderBy('nama', 'ASC')->get();
            return ResponseFormatter::success_ok(
                'Berhasil Mendapatkan Data',
                $poli
            );

        }catch (Exception $e){
            return ResponseFormatter::internal_server_error(
                'Ada yang Error Dari Server',
                $e
            );
        }
    }
}
