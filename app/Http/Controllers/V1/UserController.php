<?php

namespace App\Http\Controllers\V1;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request){
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

            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'User Registered');

        }catch (Exception $e){
            return ResponseFormatter::error([
                'message' => 'something went wrong',
                'error' => $e
            ], 'Authentication Failed', 500);
        }
    }

    public function login(Request $request){
        try {
            $request->validate([
                'email' => 'email|required',
                'password' => 'required'
            ]);

            $credentials = request(['email', 'password']);
            if(!Auth::attempt($credentials)){
                return ResponseFormatter::error([
                    'message' => 'Unathorized'
                ], 'Authentication Failed', 500);
            }

            $user = User::where('email', $request->email)->first();

            if(!Hash::check($request->password, $user->password, [])){
                throw new \Exception('Invalid Credentials');
            }

            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Authenticated');
        } catch (Exception $e) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $e
            ], 'Authentication Failed', 500);
        }
    }

    public function tampilkanProfileUser(Request $request){
        try{
            return ResponseFormatter::success(
                $request->user(), 
                'Data Profile User Berhasil Diambil'
            );
        }catch (Exception $e){
            return ResponseFormatter::error([
                'message' => 'Silahkan Login Ulang',
                'data' => $e
            ], 'Unauthorized', 500);
        }
    }

    public function lupaPassword(Request $request){
        try{
            $id = $request->id;
            $email = $request->email;
            $password = $request->password;
            $password_hash = Hash::make($password);

            $update_akun_saya = User::find($id);
            $update_akun_saya->email = $email;
            $update_akun_saya->password = $password_hash;

            $otp = Otp::find($email);

            $otp->delete();
            $update_akun_saya->save();

            return ResponseFormatter::success([
                'status' => 200,
                'message' => 'Berhasil Dihapus OTP dan Mengubah Data di profil'
            ],'Success');
        }catch (Exception $e){
            return ResponseFormatter::error([
                'message' => 'Gagal',
                'data' => $e
            ], 'Unauthorized', 500);
        }
        
    }

    public function logout(Request $request){
        $token = $request->user()->currentAccessToken()->delete();

        return ResponseFormatter::success($token, 'Token Revoked');
    }
}
