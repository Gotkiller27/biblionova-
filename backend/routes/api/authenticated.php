<?php

use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\DocumentRevisionController;
use App\Http\Controllers\API\DashboardController;
use Illuminate\Support\Facades\Route;

// Authenticated auth endpoints
Route::post('/auth/logout', [AuthController::class, 'logout']);
Route::get('/auth/me', [AuthController::class, 'me']);
Route::post('/auth/refresh', [AuthController::class, 'refresh']);
Route::post('/auth/change-password', [AuthController::class, 'changePassword']);

// Profile endpoints
Route::get('/profile', [ProfileController::class, 'show']);
Route::put('/profile', [ProfileController::class, 'update']);
Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar']);
Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar']);
Route::post('/profile/change-password', [ProfileController::class, 'changePassword']);

// Document Revisions
Route::get('document-revisions/reference/{reference_id}', [DocumentRevisionController::class, 'byReference'])->name('document-revisions.by-reference');
Route::apiResource('document-revisions', DocumentRevisionController::class);

// Dashboards
Route::middleware(['check.role:admin'])->get('/dashboard/admin', [DashboardController::class, 'admin']);
Route::middleware(['check.role:bibliothecaire'])->get('/dashboard/bibliothecaire', [DashboardController::class, 'bibliothecaire']);
Route::middleware(['check.role:responsable_validation'])->get('/dashboard/responsable', [DashboardController::class, 'responsable']);
