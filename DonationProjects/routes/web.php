<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;
Route::get('/', [ProjectController::class, 'index']);
Route::resource('projects', ProjectController::class)->only(['index', 'show']);

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::resource('projects', ProjectController::class)->except(['index', 'show']);
    Route::post('projects/{project}/donations', [DonationController::class, 'store'])->name('projects.donations.store');
});

require __DIR__.'/auth.php';
