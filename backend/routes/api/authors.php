<?php

use App\Http\Controllers\API\AuthorController;
use Illuminate\Support\Facades\Route;

Route::apiResource('authors', AuthorController::class);
Route::post('/authors/{author}/restore', [AuthorController::class, 'restore']);
Route::delete('/authors/{author}/force-delete', [AuthorController::class, 'forceDelete']);
Route::get('/authors/statistics', [AuthorController::class, 'statistics']);
Route::get('/authors/{author}/statistics', [AuthorController::class, 'authorStatistics']);
Route::get('/authors/search', [AuthorController::class, 'search']);
Route::get('/authors/nationalities', [AuthorController::class, 'nationalities']);
Route::get('/authors/top', [AuthorController::class, 'topAuthors']);
Route::get('/authors/recent', [AuthorController::class, 'recentAuthors']);
Route::get('/authors/{author}/co-authors', [AuthorController::class, 'coAuthors']);
Route::post('/authors/{author}/co-authors', [AuthorController::class, 'addCoAuthor']);
Route::delete('/authors/{author}/co-authors', [AuthorController::class, 'removeCoAuthor']);
