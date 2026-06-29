<?php

use App\Http\Controllers\API\KeywordController;
use Illuminate\Support\Facades\Route;

Route::apiResource('keywords', KeywordController::class);
Route::post('/keywords/{keyword}/restore', [KeywordController::class, 'restore']);
Route::delete('/keywords/{keyword}/force-delete', [KeywordController::class, 'forceDelete']);
Route::get('/keywords/statistics', [KeywordController::class, 'statistics']);
Route::get('/keywords/{keyword}/statistics', [KeywordController::class, 'keywordStatistics']);
Route::get('/keywords/search', [KeywordController::class, 'search']);
Route::get('/keywords/suggestions', [KeywordController::class, 'suggestions']);
Route::get('/keywords/tag-cloud', [KeywordController::class, 'tagCloud']);
Route::get('/keywords/top', [KeywordController::class, 'topKeywords']);
Route::get('/keywords/recent', [KeywordController::class, 'recentKeywords']);
Route::get('/keywords/trending', [KeywordController::class, 'trendingKeywords']);
Route::post('/keywords/import', [KeywordController::class, 'import']);
Route::get('/keywords/export', [KeywordController::class, 'export']);
