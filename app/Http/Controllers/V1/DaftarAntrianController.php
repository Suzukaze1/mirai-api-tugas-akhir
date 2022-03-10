<?php

namespace App\Http\Controllers\V1;

use App\Dummy\DataDummy;
use Illuminate\Http\Request;
use App\Models\V1\DaftarAntrian;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Exception;

class DaftarAntrianController extends Controller
{
    public function getDaftarAntrian()
    {
        $dumDaftarAntrian = DataDummy::dummyDaftarAntrian();
        try{
            //$list = DaftarAntrian::orderBy('id', 'ASC')->get();
            return ResponseFormatter::success_ok(
                'Berhasil Mendapatkan Data',
                $dumDaftarAntrian
            );

        }catch (Exception $e){
            return ResponseFormatter::internal_server_error(
                'Ada yang Error Dari Server',
                $e
            );
        }
    }

    public function getDetailListAntrian(Request $request)
    {
        $antrian = $request->input('id_antrian');
        if($antrian == "1")
        {
            $dumDetailDaftarAntrianPendaftaran = DataDummy::dummyDetailDaftarAntrianPendaftaran();

            try{
                //$list = DaftarAntrian::orderBy('id', 'ASC')->get();
                return ResponseFormatter::success_ok(
                    'Berhasil Mendapatkan Data',
                    $dumDetailDaftarAntrianPendaftaran
                );

            }catch (Exception $e){
                return ResponseFormatter::internal_server_error(
                    'Ada yang Error Dari Server',
                    $e
                );
            }
        }
        elseif($antrian == "2")
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
        elseif($antrian == "3")
        {
            $dumDetailDaftarAntrianApotekk = DataDummy::dummyDetailDaftarAntrianApotek();
            try{
                //$list = DaftarAntrian::orderBy('id', 'ASC')->get();
                return ResponseFormatter::success_ok(
                    'Berhasil Mendapatkan Data',
                    $dumDetailDaftarAntrianApotekk
                );

            }catch (Exception $e){
                return ResponseFormatter::internal_server_error(
                    'Ada yang Error Dari Server',
                    $e
                );
            }
        }
    }

    public function getDaftarAntrianPoliklinik(Request $request)
    {
        $id_poli = $request->input('id_poli');
        $dumDetailDaftarAntrianPoliklinik = DataDummy::dummyDetailDaftarAntrianPoliklinik($id_poli);
        try{
            //$list = DaftarAntrian::orderBy('id', 'ASC')->get();
            return ResponseFormatter::success_ok(
                'Berhasil Mendapatkan Data',
                $dumDetailDaftarAntrianPoliklinik
            );

        }catch (Exception $e){
            return ResponseFormatter::internal_server_error(
                'Ada yang Error Dari Server',
                $e
            );
        }
    }

}
