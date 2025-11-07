<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

Route::get('dashboard', DashboardController::class)->name('api.dashboard');

Route::apiResource('projects', ProjectController::class)
    ->except(['create', 'edit'])
    ->names('api.projects');

Route::get('projects/{project}/donations', [DonationController::class, 'index'])
    ->name('api.projects.donations.index');
Route::post('donations', [DonationController::class, 'store'])
    ->name('api.donations.store');
