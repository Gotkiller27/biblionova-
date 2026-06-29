<?php

use App\Http\Controllers\API\StatisticsController;
use Illuminate\Support\Facades\Route;

Route::get('/statistics', [StatisticsController::class, 'index']);
Route::get('/statistics/downloads', [StatisticsController::class, 'downloads']);
Route::get('/statistics/views', [StatisticsController::class, 'views']);
