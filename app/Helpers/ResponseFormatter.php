<?php

namespace App\Helpers;

class ResponseFormatter
{
  protected static $response = [
    'code' => 200,
    'status' => 'Berhasil',
    'data' => null
  ];

  public static function success_ok($status = null, $data = null){
    self::$response['code'] = CodeStatus::$code_ok;
    self::$response['status'] = $status;
    self::$response['data'] = $data;

    return response()->json(self::$response, self::$response['code'])->header('Accept', 'application/json');
  }

  public static function error_not_found($status = null, $data = null){
    self::$response['code'] = CodeStatus::$code_not_found;
    self::$response['status'] = $status;
    self::$response['data'] = $data;

    return response()->json(self::$response, self::$response['code'])->header('Accept', 'application/json');
  }

  public static function internal_server_error($status = null, $data = null){
    self::$response['code'] = CodeStatus::$code_internal_server_error;
    self::$response['status'] = $status;
    self::$response['data'] = $data;

    return response()->json(self::$response, self::$response['code'])->header('Accept', 'application/json');
  }

  public static function forbidden($status = null, $data = null){
    self::$response['code'] = CodeStatus::$code_forbidden;
    self::$response['status'] = $status;
    self::$response['data'] = $data;

    return response()->json(self::$response, self::$response['code'])->header('Accept', 'application/json');
  }

}