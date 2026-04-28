<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TranslationController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/token', [AuthController::class, 'store']);
Route::get('/auth/token', static function () {
    return response()->json([
        'message' => 'Use POST /api/auth/token with email, password, and device_name.',
    ]);
});

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/translations', [TranslationController::class, 'index']);
    Route::post('/translations', [TranslationController::class, 'store']);
    Route::get('/translations/export', [TranslationController::class, 'export']);
    Route::get('/translations/{translation}', [TranslationController::class, 'show']);
    Route::put('/translations/{translation}', [TranslationController::class, 'update']);
});
