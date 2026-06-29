<?php

use App\Http\Controllers\Api\FavoriteController;
use Illuminate\Support\Facades\Route;

Route::apiResource('favorites', FavoriteController::class);
Route::post('references/{reference}/favorite', [FavoriteController::class, 'toggle'])->name('references.favorite');
Route::get('favorites/check/{reference}', [FavoriteController::class, 'check'])->name('favorites.check');
Route::get('favorites/references', [FavoriteController::class, 'references'])->name('favorites.references');
