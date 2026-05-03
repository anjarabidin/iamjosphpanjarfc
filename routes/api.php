<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KeywordController;

Route::prefix('v1')->middleware('validate_api_key')->group(function () {});

// Internal API for Reviewers & Keywords — protected by API key
Route::middleware('validate_api_key')->group(function () {
    Route::get('journal/{journal}/reviewers', [\App\Http\Controllers\Api\ReviewerApiController::class, 'index'])->name('api.journal.reviewers');
    Route::get('keywords', [KeywordController::class, 'index'])->name('api.keywords');
});

