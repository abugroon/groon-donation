<?php

use App\Http\Controllers\Admin\BankAccountController;
use App\Http\Controllers\Admin\DonationReviewController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::get('projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::put('projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

    Route::prefix('admin')->as('admin.')->group(function () {
        Route::resource('bank-accounts', BankAccountController::class)->except(['show']);
        Route::get('projects/{project}', [AdminProjectController::class, 'show'])->name('projects.show');
        Route::get('donations/{donation}/review', [DonationReviewController::class, 'show'])->name('donations.review');
        Route::put('donations/{donation}/review', [DonationReviewController::class, 'update'])->name('donations.updateStatus');
    });
});

Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('projects/{project}', [ProjectController::class, 'show'])->name('projects.show');

Route::post('donations', [DonationController::class, 'store'])->name('donations.store');

require __DIR__.'/settings.php';
