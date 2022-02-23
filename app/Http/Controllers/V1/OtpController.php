<?php

namespace App\Http\Controllers\V1;

use Exception;
use App\Models\V1\Otp;
use App\Models\User;
use App\Mail\MyTestMail;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Models\User as ModelsUser;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Facade\FlareClient\Http\Response;

class OtpController extends Controller
{
    public function dapatkanKodeOtpLupaPassword(Request $request)
    {
        try{
            // input email
            $email = $request->email;

            // angka random untuk otp diubah menjadi hash
            $pass= rand(1000, 9999);
            $kode_otp = Hash::make($pass);

            // waktu expired untuk otp
            //$date_now = time();
            $date_expired = time()+1800;
            // $date = $date_now - $date_expired;

            // email mengcek db
            $user = User::where('email', $email)->first();

            if($user === null){
                return ResponseFormatter::error_not_found(
                    'Email Tidak Terdaftar',
                    null
                );
            }

            // cek email di table otp
            $otp = Otp::whereEmail($email)->first();
            
            $getEmail =  $user->email;

            $response = [];
            $response['email'] = $email;
            $response['kode_otp'] = $pass;

            if($user =! $otp){
                $create_otp = new Otp();
                $create_otp->email = $email;
                $create_otp->kode_otp = $kode_otp;
                $create_otp->expired_time = $date_expired;
                $create_otp->save();

                $details = [
                    'title' => 'MIRAI Pasien OTP',
                    'body' => 'OTP Untuk Lupa/Ganti Password',
                    'hash_otp' => $pass
                ];

                Mail::to($email)->send(new MyTestMail($details));

                return ResponseFormatter::success_ok(
                    "Berhasil Membuat OTP", 
                    $response
                );
            }else if ($getEmail == $email){
                $update_otp = Otp::find($otp->id);
                $update_otp->email = $email;
                $update_otp->kode_otp = $kode_otp;
                $update_otp->expired_time = $date_expired;
                $update_otp->save();

                $details = [
                    'title' => 'MIRAI Pasien OTP',
                    'body' => 'OTP Untuk Lupa/Ganti Password',
                    'hash_otp' => $pass
                ];
        
                Mail::to($email)->send(new MyTestMail($details));

                return ResponseFormatter::success_ok(
                    'Berhasil Update OTP',
                    $response
                );
            }else{
                return ResponseFormatter::internal_server_error(
                    'Kesalahan Pada Server',
                    $user
                );
            }
        }catch(Exception $e){   
            return ResponseFormatter::internal_server_error(
                'Kesalahan Pada Server',
                $e
            );
        }
    }

    public function konfirmasiKodeOtpLupaPassword(Request $request)
    {
        try{
            $email = $request->email;
            $kode_otp = $request->kode_otp;

            // email mengcek db
            $otp = Otp::where('email', $email)->first();

            // cek apakah ada data? jika tidak return
            if($otp === null){
                return ResponseFormatter::error_not_found(
                    'Email Tidak Terdaftar',
                    null
                );
            }

            $otpHash =  $otp->kode_otp;
            $expired_time = $otp->expired_time;
            $getOtp = Hash::check($kode_otp, $otpHash);

            $response = [];
            $response['email'] = $email;
            $response['kode_otp'] = intval($kode_otp);

            if($kode_otp == $getOtp){
                if($expired_time >= time()){
                    return ResponseFormatter::success_ok(
                        'Berhasil Validasi OTP',
                        $response
                    );
                }else{
                    return ResponseFormatter::error_not_found(
                        'Kode OTP Sudah Expired',
                        null
                    );
                }
            }else{
                return ResponseFormatter::error_not_found(
                    'Kode OTP Salah',
                    null
                );
            }
        }catch (Exception $e){
            return ResponseFormatter::internal_server_error(
                'Kesalahan Pada Server',
                $e
            );
        }
    }
}
