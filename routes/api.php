<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\PublicPortfolioController;
use App\Http\Controllers\WorkSampleController;
use Illuminate\Support\Facades\Route;

Route::get('/media/{path}', [MediaController::class, 'show'])->where('path', '.*');

Route::get('/portfolio/folders', [PublicPortfolioController::class, 'index']);
Route::get('/portfolio/folders/{folder}', [PublicPortfolioController::class, 'show']);

Route::post('/admin/login', [AdminAuthController::class, 'login']);

Route::middleware('admin.api')->prefix('admin')->group(function (): void {
    Route::post('/logout', [AdminAuthController::class, 'logout']);

    Route::apiResource('folders', FolderController::class);

    Route::post('/folders/{folder}/samples', [WorkSampleController::class, 'store']);
    Route::put('/samples/{workSample}', [WorkSampleController::class, 'update']);
    Route::delete('/samples/{workSample}', [WorkSampleController::class, 'destroy']);
});
