<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\AuthorController;
use App\Http\Controllers\API\PublisherController;
use App\Http\Controllers\API\KeywordController;
use App\Http\Controllers\API\ReferenceController;
use App\Http\Controllers\API\DepositRequestController;
use App\Http\Controllers\Api\DepositRequestReviewController;
use App\Http\Controllers\Api\DocumentRevisionController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\StatisticsController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Auth routes (public)
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::get('/auth/check', [AuthController::class, 'checkAuth']);
    
    Route::middleware('throttle:5,60')->group(function () {
        Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);
    });

    // Protected routes (require auth)
    Route::middleware(['auth:sanctum', 'check.account.status'])->group(function () {
        // Auth endpoints
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

        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);

        // Statistics
        Route::get('/statistics', [StatisticsController::class, 'index']);
        Route::get('/statistics/downloads', [StatisticsController::class, 'downloads']);
        Route::get('/statistics/views', [StatisticsController::class, 'views']);

        // User management routes
        Route::middleware(['check.role:admin,responsable_rh'])->group(function () {
            Route::apiResource('users', UserController::class);
            Route::post('/users/{user}/restore', [UserController::class, 'restore']);
            Route::delete('/users/{user}/force-delete', [UserController::class, 'forceDelete']);
            Route::get('/users/{user}/roles', [UserController::class, 'getRoles']);
            Route::post('/users/{user}/assign-role', [UserController::class, 'assignRole']);
            Route::delete('/users/{user}/remove-role', [UserController::class, 'removeRole']);
            Route::post('/users/{user}/sync-roles', [UserController::class, 'syncRoles']);
            Route::get('/users/{user}/permissions', [UserController::class, 'getPermissions']);
            Route::post('/users/{user}/assign-permission', [UserController::class, 'assignPermission']);
            Route::delete('/users/{user}/remove-permission', [UserController::class, 'removePermission']);
            Route::post('/users/{user}/sync-permissions', [UserController::class, 'syncPermissions']);
            Route::put('/users/{user}/status', [UserController::class, 'updateStatus']);
            Route::get('/users/export', [UserController::class, 'export']);
        });

        // Categories
        Route::apiResource('categories', CategoryController::class);

        // Authors
        Route::apiResource('authors', AuthorController::class);

        // Publishers
        Route::apiResource('publishers', PublisherController::class);

        // Keywords
        Route::apiResource('keywords', KeywordController::class);

        // References
        Route::apiResource('references', ReferenceController::class);
        Route::get('references/{reference}/download', [ReferenceController::class, 'download'])->name('references.download');

        // Deposit Requests
        Route::get('deposit-requests/my-requests', [DepositRequestController::class, 'myRequests'])->name('deposit-requests.my-requests');
        Route::get('deposit-requests/assigned', [DepositRequestController::class, 'assigned'])->name('deposit-requests.assigned');
        Route::get('deposit-requests/pending', [DepositRequestController::class, 'pending'])->name('deposit-requests.pending');
        Route::apiResource('deposit-requests', DepositRequestController::class);
        Route::post('deposit-requests/{deposit_request}/assign', [DepositRequestController::class, 'assign'])->name('deposit-requests.assign');
        Route::post('deposit-requests/{deposit_request}/approve', [DepositRequestController::class, 'approve'])->name('deposit-requests.approve');
        Route::post('deposit-requests/{deposit_request}/reject', [DepositRequestController::class, 'reject'])->name('deposit-requests.reject');
        Route::post('deposit-requests/{deposit_request}/second-review', [DepositRequestController::class, 'secondReview'])->name('deposit-requests.second-review');
        Route::post('deposit-requests/{deposit_request}/publish', [DepositRequestController::class, 'publish'])->name('deposit-requests.publish');

        // Deposit Request Reviews
        Route::apiResource('deposit-request-reviews', DepositRequestReviewController::class)->except(['store']);

        // Document Revisions
        Route::get('document-revisions/reference/{reference_id}', [DocumentRevisionController::class, 'byReference'])->name('document-revisions.by-reference');
        Route::apiResource('document-revisions', DocumentRevisionController::class);

        // Dashboards
        Route::middleware(['check.role:admin'])->get('/dashboard/admin', [DashboardController::class, 'admin']);
        Route::middleware(['check.role:bibliothecaire'])->get('/dashboard/bibliothecaire', [DashboardController::class, 'bibliothecaire']);
        Route::middleware(['check.role:responsable_validation'])->get('/dashboard/responsable', [DashboardController::class, 'responsable']);
    });
});
