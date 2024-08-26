<?php
namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;

trait ResponseHandler
{
    public static function successResponse(string $msg = null, $data = null, int $statusCode = 200): JsonResponse
    {
        return response()->json(["status" => $statusCode, "msg" => $msg, "data" => $data], $statusCode);
    }

    public static function errorResponse(string $error = null, int $statusCode = 400): JsonResponse
    {
        return response()->json(["status" => $statusCode, "error" => $error], $statusCode);
    }

}
