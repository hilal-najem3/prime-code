<?php

namespace App\Http\Controllers;

use App\Support\ApiResponse;

abstract class ApiController extends Controller
{
    protected function success(
        mixed $data = null,
        ?string $message = null,
        int $status = 200,
        mixed $meta = null
    ) {
        return ApiResponse::success(
            $data,
            $message,
            $status,
            $meta
        );
    }

    protected function error(
        string $message = 'Error',
        int $status = 400,
        mixed $errors = null
    ) {
        return ApiResponse::error(
            $message,
            $status,
            $errors
        );
    }
}
