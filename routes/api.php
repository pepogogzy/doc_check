<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\RuleController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/documents', [DocumentController::class, 'store']);
    Route::get('/documents', [DocumentController::class, 'index']);
    Route::get('/documents/{document}', [DocumentController::class, 'show']);
    Route::post('/documents/{document}/analyze', [DocumentController::class, 'analyze']);

    Route::post('/rules', [RuleController::class, 'store']);
    Route::get('/rules', [RuleController::class, 'index']);
    Route::put('/rules/{rule}', [RuleController::class, 'update']);

    Route::get('/audit-logs', [AuditLogController::class, 'index']);
});
