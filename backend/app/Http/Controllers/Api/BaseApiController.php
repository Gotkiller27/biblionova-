<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

abstract class BaseApiController extends Controller
{
    /**
     * Réponse de succès
     */
    protected function success($data = null, string $message = 'Opération réussie', int $statusCode = 200): JsonResponse
    {
        return ApiResponse::success($data, $message, $statusCode);
    }

    /**
     * Réponse d'erreur
     */
    protected function error(string $message = 'Une erreur est survenue', array $errors = [], int $statusCode = 400): JsonResponse
    {
        return ApiResponse::error($message, $errors, $statusCode);
    }

    /**
     * Réponse de validation échouée
     */
    protected function validationError(array $errors, string $message = 'Erreur de validation'): JsonResponse
    {
        return ApiResponse::validationError($errors, $message);
    }

    /**
     * Réponse non autorisée
     */
    protected function unauthorized(string $message = 'Non autorisé'): JsonResponse
    {
        return ApiResponse::unauthorized($message);
    }

    /**
     * Réponse interdite
     */
    protected function forbidden(string $message = 'Accès interdit'): JsonResponse
    {
        return ApiResponse::forbidden($message);
    }

    /**
     * Réponse introuvable
     */
    protected function notFound(string $message = 'Ressource introuvable'): JsonResponse
    {
        return ApiResponse::notFound($message);
    }

    /**
     * Réponse de création réussie
     */
    protected function created($data = null, string $message = 'Ressource créée avec succès'): JsonResponse
    {
        return ApiResponse::created($data, $message);
    }

    /**
     * Réponse de suppression réussie
     */
    protected function deleted(string $message = 'Ressource supprimée avec succès'): JsonResponse
    {
        return ApiResponse::deleted($message);
    }

    /**
     * Réponse sans contenu
     */
    protected function noContent(): JsonResponse
    {
        return ApiResponse::noContent();
    }

    /**
     * Réponse de succès (alias pour compatibilité)
     */
    protected function sendResponse($data = null, string $message = 'Opération réussie', int $statusCode = 200): JsonResponse
    {
        return $this->success($data, $message, $statusCode);
    }

    /**
     * Réponse d'erreur (alias pour compatibilité)
     */
    protected function sendError(string $message = 'Une erreur est survenue', array $errors = [], int $statusCode = 400): JsonResponse
    {
        return $this->error($message, $errors, $statusCode);
    }
}
