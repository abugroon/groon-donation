<?php

use App\Http\Controllers\DonationController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('projects', ProjectController::class);
    Route::get('projects/{project}/donations', [DonationController::class, 'index']);
    Route::post('donations', [DonationController::class, 'store']);
});
