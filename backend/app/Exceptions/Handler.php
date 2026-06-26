<?php

namespace App\Exceptions;

use App\Helpers\ApiResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
    */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Exception caught:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        // Si c'est une requête API, retourner format standardisé
        if ($request->is('api/*') || $request->expectsJson()) {
            return $this->handleApiException($request, $e);
        }

        return parent::render($request, $e);
    }

    /**
     * Handle API exceptions with standardized format
     */
    protected function handleApiException($request, Throwable $e)
    {
        // Validation errors
        if ($e instanceof ValidationException) {
            return ApiResponse::validationError(
                $e->errors(),
                'Erreur de validation'
            );
        }

        // Authentication errors
        if ($e instanceof AuthenticationException) {
            return ApiResponse::unauthorized('Non authentifié');
        }

        // Authorization errors
        if ($e instanceof AuthorizationException) {
            return ApiResponse::forbidden('Action non autorisée');
        }

        // Model not found
        if ($e instanceof ModelNotFoundException) {
            return ApiResponse::notFound('Ressource introuvable');
        }

        // Route not found
        if ($e instanceof NotFoundHttpException) {
            return ApiResponse::notFound('Point de terminaison introuvable');
        }

        // Method not allowed
        if ($e instanceof MethodNotAllowedHttpException) {
            return ApiResponse::error('Méthode HTTP non autorisée', null, 405);
        }

        // Server errors (500)
        if (config('app.debug')) {
            return ApiResponse::serverError($e->getMessage());
        }

        return ApiResponse::serverError('Une erreur est survenue');
    }

    /**
     * Convert an authentication exception into a response.
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return ApiResponse::unauthorized('Non authentifié');
        }

        return redirect()->guest(route('login'));
    }
}
