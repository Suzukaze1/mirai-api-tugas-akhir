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
    self::$response['code'] = CodeStatus::$SUCCESS;
    self::$response['status'] = $status;
    self::$response['data'] = $data;

    return response()->json(self::$response, self::$response['code'])->header('Accept', 'application/json');
  }

  public static function error_not_found($status = null, $data = null){
    self::$response['code'] = CodeStatus::$NOT_FOUND;
    self::$response['status'] = $status;
    self::$response['data'] = $data;

    return response()->json(self::$response, self::$response['code'])->header('Accept', 'application/json');
  }

  public static function internal_server_error($status = null, $data = null){
    self::$response['code'] = CodeStatus::$INTERNAL_SERVER_ERROR;
    self::$response['status'] = $status;
    self::$response['data'] = $data;

    return response()->json(self::$response, self::$response['code'])->header('Accept', 'application/json');
  }

  public static function forbidden($status = null, $data = null){
    self::$response['code'] = CodeStatus::$FORBIDDEN;
    self::$response['status'] = $status;
    self::$response['data'] = $data;

    return response()->json(self::$response, self::$response['code'])->header('Accept', 'application/json');
  }

}