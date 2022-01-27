<?php

namespace App\Http\Controllers\V1;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\User;
use Exception;
use Facade\FlareClient\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class OtpController extends Controller
{
    public function dapatkanKodeOtpLupaPassword(Request $request){
        // input email
        $email = $request->email;
        // angka random untuk otp diubah menjadi hash
        $pass= rand(1000, 9999);
        $kode_otp = Hash::make($pass);

        // waktu expired untuk otp
        $date_now = time();
        $date_expired = time()+1800;
        $date = $date_now - $date_expired;
        // if($date <= -1){
        //     echo "Berhasil";
        // }else{
        //     echo "GAGAL";
        // }

        // email mengcek db
        $user = User::where('email', $email)->first();
        
        // $checkDb = isset($user) ? $user[0] : false;
        
        if($user === null){
            return ResponseFormatter::error([
                'message' => 'Email Tidak Terdaftar'
            ], 'Gagal', 404);
        }

        $getEmail =  $user->email;

        try{
            if($getEmail == $email){
                $update_otp = Otp::find($email);
                $update_otp->email = $email;
                $update_otp->kode_otp = $kode_otp;
                $update_otp->expired_time = $date_expired;
                $update_otp->save();

                return ResponseFormatter::success([
                    'status' => 200,
                    'message' => 'berhasil update otp'
                ], $pass);
            }else if ($getEmail === null){
                return ResponseFormatter::error([
                    'message' => 'Email Tidak Terdaftar'
                ], 'Gagal', 404);
            }

            Otp::create([
                'email' => $email,
                'kode_otp' => $kode_otp,
                'expired_time' => $date_expired
            ]);

            return ResponseFormatter::success([
                'status' => 200,
                'message' => 'berhasil membuat otp'
            ], $pass);
            
        }catch (Exception $e){
            return ResponseFormatter::error([
                'message' => 'Gagal Kirim Otp',
                'error' => $e
            ], 'Authentication Failed', 500);
        }
    }

    public function konfirmasiKodeOtpLupaPassword(Request $request){
        try{
            $email = $request->email;
            $kode_otp = $request->kode_otp;

            // email mengcek db
            $otp = Otp::where('email', $email)->first();

            // cek apakah ada data? jika tidak return
            if($otp === null){
                return ResponseFormatter::error([
                    'message' => 'Email Tidak Terdaftar'
                ], 'Gagal', 404);
            }

            $otpHash =  $otp->kode_otp;
            $expired_time = $otp->expired_time;
            $getOtp = Hash::check($kode_otp, $otpHash);

            

            if($kode_otp == $getOtp){
                if($expired_time >= time()){
                    return ResponseFormatter::success([
                        'status' => 200,
                        'message' => 'Berhasil Validasi OTP'
                    ], 'Success');
                }else{
                    return ResponseFormatter::error([
                        'message' => 'Kode OTP Sudah Expired'
                    ], 'Gagal', 500);
                }
            }else{
                return ResponseFormatter::error([
                    'message' => 'OTP Salah'
                ], 'Gagal', 500);
            }
        }catch (Exception $e){
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $e
            ], 'Authentication Failed', 500);
        }
    }
}
