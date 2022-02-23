<?php

namespace App\Http\Controllers\V1;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\V1\Otp;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        return "KLLELE";
        die();
        try{
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string']
            ]);

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            $user = User::where('email', $request->email)->first();

            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return ResponseFormatter::success_ok([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'User Registered');

        }catch (Exception $e){
            return ResponseFormatter::error_not_found([
                'message' => 'something went wrong',
                'error' => $e
            ], 'Authentication Failed', 500);
        }
    }

    public function login(Request $request)
    {
        $getTime = Carbon::now()->addMinute(10);
        $exp_time = $getTime->format('Y-m-d H:i:s');
        try {
            $request->validate([
                'email' => 'email|required',
                'password' => 'required'
            ]);
    
            $credentials = request(['email', 'password']);
            if(!Auth::attempt($credentials)){
                return ResponseFormatter::forbidden(
                    'Password Salah',
                    null
                );
            }
    
            $user = User::where('email', $request->email)->first();

            $kode_rm = sprintf("%08s", strval($user->kode));

            if(!Hash::check($request->password, $user->password, [])){
                throw new \Exception('Invalid Credentials');
            }

            $tokenResult = $user->createToken('authToken')->plainTextToken;

            $response = [];
            $response['id'] = $user->id;
            $response['email'] = $user->email;
            $response['password'] = $user->password; 
            $response['nomor_rekam_medik'] = $kode_rm;
            $response['access_token'] = $tokenResult;
            $response['token_expired'] = $exp_time;

            return ResponseFormatter::success_ok(
                'Berhasil Login', $response);
        } catch (Exception $e) {
            return ResponseFormatter::internal_server_error(
                'Kesalahan Pada Server',
                $e
            );
        }
    }

    public function tampilkanProfileUser(Request $request)
    {
        return ResponseFormatter::success_ok(
            'Data Profile User Berhasil Diambil',
            $request->user()
        );
    }

    public function lupaPassword(Request $request)
    {
        try{
            $email = $request->email;
            $password = $request->password;
            $password_hash = Hash::make($password);

            // cek email di table user
            $otp1 = user::where('email', $email)->first();

            // cek email di table otp
            $otp_email = otp::where('email', $email)->first();

            $update_akun_saya = User::find($otp1->id);
            
            $update_akun_saya->email = $email;
            $update_akun_saya->password = $password_hash;

            $response = [];
            $response['email'] = $email;
            $response['password'] = $password;

            $otp = Otp::find($otp_email->id);

            if($otp){
                $otp->delete();
                $update_akun_saya->save();

                return ResponseFormatter::success_ok(
                    'Berhasil Dihapus OTP dan Mengubah Data di profil',
                    $response
                );
            }else{
                return ResponseFormatter::error_not_found(
                    'Tidak Ditemukan',
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

    public function cekPasswordGantiPassword(Request $request)
    {
        try{
            $email = $request->email;
            $password = $request->password;

            $response = [];
            $response['email'] = $email;
            $response['password'] = $password;

            if(!$user = User::where('email', $email)->first()) return ResponseFormatter::error_not_found("Email Tidak Ditemukan", null);
            if(!Hash::check($password, $user->password, [])) return ResponseFormatter::error_not_found("Password Salah", null);
            return ResponseFormatter::success_ok("Password Benar", $response);
        }catch (Exception $e){
            return ResponseFormatter::internal_server_error("Ada Yang Salah Pada Server", $e);
        }
    }

    public function gantiPassword(Request $request)
    {
        try{
            $email = $request->email;
            $password_lama = $request->password_lama;
            $password_baru = $request->password_baru;
            $ulangi_password_baru = $request->ulangi_password_baru;

            $response = [];
            $response['email'] = $email;
            $response['password_lama'] = $password_lama;
            $response['password_baru'] = $password_baru;
            $response['ulangi_password_baru'] = $ulangi_password_baru;

            if(!$user = User::where('email', $email)->first()) return ResponseFormatter::error_not_found("Email Tidak Ditemukan", null);
            if(!Hash::check($password_lama, $user->password, [])) return ResponseFormatter::error_not_found("Password Salah", null);
            if($password_baru != $ulangi_password_baru) return ResponseFormatter::error_not_found("Password Baru Tidak Sama", null);

            $ganti_password = User::find($user->id);
            $ganti_password->password = Hash::make($password_baru);
            $ganti_password->save();

            return ResponseFormatter::success_ok("Berhasil Ganti Password", $response);
        }catch (Exception $e){
            return ResponseFormatter::internal_server_error("Ada Yang Salah Pada Server", $e);
        }
    }

    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken()->delete();

        return ResponseFormatter::success_ok($token, 'Token Revoked/Dihapus');
    }
}
