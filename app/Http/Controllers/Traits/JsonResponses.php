<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\JsonResponse;

trait JsonResponses
{
    protected function successResponse(mixed $data = null, int $status = 200): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $data], $status);
    }

    protected function errorResponse(string $message, int $status = 400): JsonResponse
    {
        return response()->json(['success' => false, 'error' => $message], $status);
    }
}
