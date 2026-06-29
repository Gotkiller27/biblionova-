<?php

use App\Http\Controllers\API\CategoryController;
use Illuminate\Support\Facades\Route;

Route::apiResource('categories', CategoryController::class);
Route::post('/categories/{category}/restore', [CategoryController::class, 'restore']);
Route::delete('/categories/{category}/force-delete', [CategoryController::class, 'forceDelete']);
Route::get('/categories/tree', [CategoryController::class, 'tree']);
Route::get('/categories/flat-list', [CategoryController::class, 'flatList']);
Route::get('/categories/statistics', [CategoryController::class, 'statistics']);
Route::get('/categories/{category}/statistics', [CategoryController::class, 'statistics']);
Route::get('/categories/{category}/available-parents', [CategoryController::class, 'availableParents']);
Route::post('/categories/{category}/move', [CategoryController::class, 'move']);
Route::get('/categories/{category}/path', [CategoryController::class, 'path']);
Route::get('/categories/search', [CategoryController::class, 'search']);
Route::get('/categories/top', [CategoryController::class, 'topCategories']);
Route::get('/categories/recent', [CategoryController::class, 'recentCategories']);
