<?php

use App\Http\Controllers\API\DepositRequestController;
use App\Http\Controllers\Api\DepositRequestReviewController;
use Illuminate\Support\Facades\Route;

Route::get('deposit-requests/my-requests', [DepositRequestController::class, 'myRequests'])->name('deposit-requests.my-requests');
Route::get('deposit-requests/assigned', [DepositRequestController::class, 'assigned'])->name('deposit-requests.assigned');
Route::get('deposit-requests/pending', [DepositRequestController::class, 'pending'])->name('deposit-requests.pending');
Route::apiResource('deposit-requests', DepositRequestController::class);
Route::post('deposit-requests/{deposit_request}/assign', [DepositRequestController::class, 'assign'])->name('deposit-requests.assign');
Route::post('deposit-requests/{deposit_request}/approve', [DepositRequestController::class, 'approve'])->name('deposit-requests.approve');
Route::post('deposit-requests/{deposit_request}/reject', [DepositRequestController::class, 'reject'])->name('deposit-requests.reject');
Route::post('deposit-requests/{deposit_request}/second-review', [DepositRequestController::class, 'secondReview'])->name('deposit-requests.second-review');
Route::post('deposit-requests/{deposit_request}/publish', [DepositRequestController::class, 'publish'])->name('deposit-requests.publish');
Route::get('deposit-requests/{deposit_request}/attachments', [DepositRequestController::class, 'attachments'])->name('deposit-requests.attachments');
Route::post('deposit-requests/{deposit_request}/attachments', [DepositRequestController::class, 'storeAttachment'])->name('deposit-requests.store-attachment');
Route::delete('deposit-requests/{deposit_request}/attachments/{attachment}', [DepositRequestController::class, 'deleteAttachment'])->name('deposit-requests.delete-attachment');
Route::post('deposit-requests/{deposit_request}/submit', [DepositRequestController::class, 'submit'])->name('deposit-requests.submit');
Route::post('deposit-requests/{deposit_request}/cancel', [DepositRequestController::class, 'cancel'])->name('deposit-requests.cancel');
Route::get('deposit-requests/drafts', [DepositRequestController::class, 'drafts'])->name('deposit-requests.drafts');

Route::apiResource('deposit-request-reviews', DepositRequestReviewController::class)->except(['store']);
