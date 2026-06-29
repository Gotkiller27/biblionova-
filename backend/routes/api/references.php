<?php

use App\Http\Controllers\API\ReferenceController;
use Illuminate\Support\Facades\Route;

Route::apiResource('references', ReferenceController::class);
Route::get('references/{reference}/download', [ReferenceController::class, 'download'])->name('references.download');
