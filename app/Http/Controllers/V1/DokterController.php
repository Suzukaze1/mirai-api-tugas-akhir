<?php

namespace App\Http\Controllers\V1;

use App\Dummy\DataDummy;
use App\Helpers\ResponseFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\V1\Dokter;
use App\Models\V1\Poli;

class DokterController extends Controller
{
    public function getDokterPerPoli(Request $request)
    {
        $id_poli = $request->input('id_poli');

        $dokter = DataDummy::dummyDokter($id_poli);

        return ResponseFormatter::success_ok("Berhasil Mendapatkan Data", $dokter);
    }
}
