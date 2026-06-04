<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait JsonResponseTrait
{
    /**
     * Create a JSON response.
     */
    protected static function response(array $data, int $status_code = Response::HTTP_OK, array $headers = [], int $options = 0): JsonResponse
    {
        return response()->json($data, $status_code, $headers, $options);
    }

    /**
     * Create a success JSON response.
     */
    protected function success(string $message = null, $data = null, int $status_code = Response::HTTP_OK, array $headers = [], int $options = 0): JsonResponse
    {
        $responseData = [
            'status'      => true,
            'message'     => $message,
            'data'        => $data,
            'status_code' => $status_code,
        ];

        return self::response($responseData, $status_code, $headers, $options);
    }

    /**
     * Create an error JSON response.
     */
    protected function error(string $message = null, int $status_code = Response::HTTP_INTERNAL_SERVER_ERROR, $errors = null, array $headers = [], int $options = 0): JsonResponse
    {
        $responseData = [
            'status'      => false,
            'message'     => $message,
            'errors'      => $errors,
            'status_code' => $status_code,
        ];

        return self::response($responseData, $status_code, $headers, $options);
    }
}
