<?php

use App\Http\Controllers\API\PublisherController;
use Illuminate\Support\Facades\Route;

Route::apiResource('publishers', PublisherController::class);
Route::post('/publishers/{publisher}/restore', [PublisherController::class, 'restore']);
Route::delete('/publishers/{publisher}/force-delete', [PublisherController::class, 'forceDelete']);
Route::get('/publishers/statistics', [PublisherController::class, 'statistics']);
Route::get('/publishers/{publisher}/statistics', [PublisherController::class, 'publisherStatistics']);
Route::get('/publishers/search', [PublisherController::class, 'search']);
Route::get('/publishers/countries', [PublisherController::class, 'countries']);
Route::get('/publishers/cities', [PublisherController::class, 'cities']);
Route::get('/publishers/top', [PublisherController::class, 'topPublishers']);
Route::get('/publishers/recent', [PublisherController::class, 'recentPublishers']);
