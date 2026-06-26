<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use Illuminate\Http\JsonResponse;

class BaseApiController extends Controller
{
    /**
     * Success response
     */
    protected function success($data = null, string $message = 'Operation successful', int $statusCode = 200): JsonResponse
    {
        return ApiResponse::success($data, $message, $statusCode);
    }

    /**
     * Error response
     */
    protected function error(string $message = 'Operation failed', $errors = null, int $statusCode = 400): JsonResponse
    {
        return ApiResponse::error($message, $errors, $statusCode);
    }

    /**
     * Validation error response
     */
    protected function validationError($errors, string $message = 'Validation failed'): JsonResponse
    {
        return ApiResponse::validationError($errors, $message);
    }

    /**
     * Unauthorized response
     */
    protected function unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return ApiResponse::unauthorized($message);
    }

    /**
     * Forbidden response
     */
    protected function forbidden(string $message = 'Forbidden'): JsonResponse
    {
        return ApiResponse::forbidden($message);
    }

    /**
     * Not found response
     */
    protected function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return ApiResponse::notFound($message);
    }

    /**
     * Server error response
     */
    protected function serverError(string $message = 'Internal server error'): JsonResponse
    {
        return ApiResponse::serverError($message);
    }

    /**
     * Paginated response
     */
    protected function paginated($paginator, string $resourceClass = null, string $message = 'Data retrieved successfully'): JsonResponse
    {
        return ApiResponse::paginated($paginator, $resourceClass, $message);
    }

    /**
     * Collection response
     */
    protected function collection($collection, string $resourceClass = null, string $message = 'Data retrieved successfully'): JsonResponse
    {
        return ApiResponse::collection($collection, $resourceClass, $message);
    }

    /**
     * Created response
     */
    protected function created($data = null, string $message = 'Resource created successfully'): JsonResponse
    {
        return ApiResponse::created($data, $message);
    }

    /**
     * No content response
     */
    protected function noContent(string $message = 'Operation successful'): JsonResponse
    {
        return ApiResponse::noContent($message);
    }
}
