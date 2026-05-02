<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ApiResponse
{
    /*
    |--------------------------------------------------------------------------
    | Success Response
    |--------------------------------------------------------------------------
    |
    | Standard success response used across the entire API.
    | Automatically detects pagination and structures the meta block.
    |
    */

    public static function success(
        mixed $data = null,
        string $message = 'Request completed successfully.',
        int $status = 200
    ): JsonResponse {

        $meta = null;

        /*
        |--------------------------------------------------------------------------
        | Handle Laravel Resources
        |--------------------------------------------------------------------------
        */
        if ($data instanceof JsonResource || $data instanceof ResourceCollection) {
            $data = $data->response()->getData(true);
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination Detection
        |--------------------------------------------------------------------------
        */

        if ($data instanceof LengthAwarePaginator) {

            $meta = [
                'current_page' => $data->currentPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'last_page' => $data->lastPage()
            ];

            $data = $data->items();
        } elseif ($data instanceof Paginator) {

            $meta = [
                'current_page' => $data->currentPage(),
                'per_page' => $data->perPage()
            ];

            $data = $data->items();
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => $meta,
            'errors' => null
        ], $status);
    }

    /*
    |--------------------------------------------------------------------------
    | Error Response
    |--------------------------------------------------------------------------
    |
    | Generic error response used for all application errors.
    |
    */

    public static function error(
        string $message = 'Something went wrong.',
        int $status = 400,
        mixed $errors = null
    ): JsonResponse {

        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'meta' => null,
            'errors' => $errors
        ], $status);
    }

    /*
    |--------------------------------------------------------------------------
    | Validation Error Response
    |--------------------------------------------------------------------------
    |
    | Standard validation response format.
    |
    */

    public static function validation(
        array $errors,
        string $message = 'Validation failed.'
    ): JsonResponse {

        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'meta' => null,
            'errors' => $errors
        ], 422);
    }

    /*
    |--------------------------------------------------------------------------
    | Not Found Response
    |--------------------------------------------------------------------------
    */

    public static function notFound(
        string $message = 'Resource not found.'
    ): JsonResponse {

        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'meta' => null,
            'errors' => null
        ], 404);
    }

    /*
    |--------------------------------------------------------------------------
    | Unauthorized Response
    |--------------------------------------------------------------------------
    */

    public static function unauthorized(
        string $message = 'Unauthorized.'
    ): JsonResponse {

        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'meta' => null,
            'errors' => null
        ], 401);
    }

    /*
    |--------------------------------------------------------------------------
    | Forbidden Response
    |--------------------------------------------------------------------------
    */

    public static function forbidden(
        string $message = 'Forbidden.'
    ): JsonResponse {

        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'meta' => null,
            'errors' => null
        ], 403);
    }
}