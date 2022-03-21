<?php

namespace App\Http\Controllers\V1;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\V1\Notif;
use Facade\FlareClient\Http\Response;
use Illuminate\Http\Request;

class NotifController extends Controller
{
    public function listNotif(Request $request)
    {
        $email = $request->input('email');
        $response = [];
        $list_notif = [];

        try
        {
            $list_notif = Notif::where('email', $email)->get();
            if(count($list_notif) == 0) return ResponseFormatter::success_ok("Belum Ada Notif", []);

            foreach ($list_notif as $ln)
            {
                $list_notif['id_notif'] = $ln->id;
                $list_notif['email'] = $email;
                $list_notif['subjek'] = $ln->subjek;
                $list_notif['isi'] = $ln->isi;
                $list_notif['is_baca'] = $ln->is_baca;
                $response[] = $list_notif;
            }
            return ResponseFormatter::success_ok("Data Notif Ditemukan", $response);
        }
        catch (\Throwable $th)
        {
            return ResponseFormatter::internal_server_error("Kesalahan Dari Server", null);
        }
    }

    public function notifDibaca(Request $request)
    {
        $email = $request->email;
        $id_notif = $request->id_notif;

        $cek_isi_notif = Notif::where('email', $email)->where('id', $id_notif)->first();
        if($cek_isi_notif == null) return ResponseFormatter::error_not_found("Notif Tidak Ditemukan", null);

        $ubah_status_baca = Notif::find($id_notif);
        $ubah_status_baca->is_baca = "1";
        $ubah_status_baca->save();
        return ResponseFormatter::success_ok("Notif Berhasil Dibaca", null);
    }

    public function hapusNotif(Request $request)
    {
        $email = $request->email;
        $id_notif = $request->id_notif;

        $cek_isi_notif = Notif::where('email', $email)->where('id', $id_notif)->first();
        if($cek_isi_notif == null) return ResponseFormatter::error_not_found("Notif Tidak Ditemukan", null);

        $ubah_status_baca = Notif::find($id_notif);
        $ubah_status_baca->delete();
        return ResponseFormatter::success_ok("Notif Berhasil Dihapus", null);
    }
}
