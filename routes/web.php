<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('rbac')->name('rbac.')->group(function () {
        Route::apiResource('activities', ActivityController::class)->only(['index', 'show']);
        Route::apiResource('announcements', AnnouncementController::class)->only(['index', 'show']);
        Route::apiResource('fees', FeeController::class)->only(['index', 'show']);

        Route::middleware('role:super_admin|jawatankuasa')->group(function () {
            Route::apiResource('activities', ActivityController::class)->only(['store', 'update', 'destroy']);
            Route::apiResource('announcements', AnnouncementController::class)->only(['store', 'update', 'destroy']);
            Route::apiResource('fees', FeeController::class)->only(['store', 'update', 'destroy']);
        });

        Route::middleware('role:super_admin|ahli')->group(function () {
            Route::post('attendances', [AttendanceController::class, 'store'])->name('attendances.store');
            Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
        });
    });
});

require __DIR__.'/auth.php';
