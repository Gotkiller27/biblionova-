<?php

use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;

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
