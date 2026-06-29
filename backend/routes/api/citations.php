<?php

use App\Http\Controllers\Api\CitationController;
use Illuminate\Support\Facades\Route;

Route::apiResource('citations', CitationController::class);
Route::get('references/{reference}/citations', [CitationController::class, 'forReference'])->name('references.citations');
Route::get('references/{reference}/citing', [CitationController::class, 'citing'])->name('references.citing');
Route::get('references/{reference}/cited-by', [CitationController::class, 'citedBy'])->name('references.cited-by');
Route::post('references/{citing}/cite/{cited}', [CitationController::class, 'cite'])->name('references.cite');
Route::get('references/{reference}/citation-count', [CitationController::class, 'count'])->name('references.citation-count');
