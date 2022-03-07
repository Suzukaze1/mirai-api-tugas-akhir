<?php

namespace App\Http\Controllers\V1;

use App\Dummy\DataDummy;
use App\Models\V1\Kamar;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Exception;

class KamarController extends Controller
{
    public function getListKamar1()
    {
        try{
            $list = Kamar::orderBy('id', 'ASC')->get();
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

    public function getListKamar()
    {
        $dumListKamar =  DataDummy::dummyListKamar();
        try{
            return ResponseFormatter::success_ok(
                'Berhasil Mendapatkan Data',
                $dumListKamar
            );

        }catch (Exception $e){
            return ResponseFormatter::internal_server_error(
                'Ada yang Error Dari Server',
                $e
            );
        }
    }

    public function getDetailKamar(Request $request){
        $id_kamar = $request->input('id_kamar');
        $dumDetailKamar =  DataDummy::dummyDetailKamar($id_kamar);
        try{
            return ResponseFormatter::success_ok(
                'Berhasil Mendapatkan Data',
                $dumDetailKamar
            );

        }catch (Exception $e){
            return ResponseFormatter::internal_server_error(
                'Ada yang Error Dari Server',
                $e
            );
        }
    }
}
