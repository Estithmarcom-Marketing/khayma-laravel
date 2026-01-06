<?php

namespace App\Traits\Response;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    public static function successResponse($data, $message = '', $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    public static function errorResponse($error, $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => $error,
        ], $status);
    }
}
