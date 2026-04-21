<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CommitteeAssociationController;
use App\Http\Controllers\CommitteePortalController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\MemberPortalController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
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

    Route::middleware(['verified', 'role:super_admin'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
            Route::get('roles', [AdminRoleController::class, 'index'])->name('roles.index');
        });

    Route::prefix('rbac')->name('rbac.')->group(function () {
        Route::apiResource('activities', ActivityController::class)->only(['index', 'show']);
        Route::apiResource('announcements', AnnouncementController::class)->only(['index', 'show']);
        Route::apiResource('fees', FeeController::class)->only(['index', 'show']);

        Route::middleware('role:super_admin|jawatankuasa|setiausaha|bendahari')->group(function () {
            Route::apiResource('activities', ActivityController::class)->only(['store', 'update', 'destroy']);
            Route::apiResource('announcements', AnnouncementController::class)->only(['store', 'update', 'destroy']);
            Route::apiResource('fees', FeeController::class)->only(['store', 'update', 'destroy']);
        });

        Route::middleware('role:ahli')->group(function () {
            Route::post('attendances', [AttendanceController::class, 'store'])->name('attendances.store');
            Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
        });
    });

    Route::prefix('member')
        ->name('member.')
        ->middleware('reject_super_admin_member_portal')
        ->group(function () {
            Route::get('associations', [MemberPortalController::class, 'associations'])->name('associations.index');
            Route::patch('associations/switch', [MemberPortalController::class, 'switchAssociation'])->name('associations.switch');

            Route::prefix('membership')->name('membership.')->group(function () {
                Route::get('profile', [MemberPortalController::class, 'membershipProfile'])->name('profile');
                Route::get('card', [MemberPortalController::class, 'membershipCard'])->name('card');
                Route::get('applications', [MemberPortalController::class, 'membershipApplications'])->name('applications');
            });

            Route::get('fees', [MemberPortalController::class, 'fees'])->name('fees.index');
            Route::get('invoices', [MemberPortalController::class, 'invoices'])->name('invoices.index');
            Route::get('payments', [MemberPortalController::class, 'payments'])->name('payments.index');
            Route::get('receipts', [MemberPortalController::class, 'receipts'])->name('receipts.index');
            Route::get('activities', [MemberPortalController::class, 'activities'])->name('activities.index');
            Route::get('attendances', [MemberPortalController::class, 'attendances'])->name('attendances.index');
            Route::get('announcements', [MemberPortalController::class, 'announcements'])->name('announcements.index');
        });

    Route::middleware('role:super_admin|jawatankuasa|pengerusi|setiausaha')
        ->prefix('committee')
        ->name('committee.')
        ->group(function () {
            Route::prefix('associations')->name('associations.')->group(function () {
                Route::get('info', [CommitteePortalController::class, 'associationInfo'])->name('info');
                Route::get('members', [CommitteePortalController::class, 'associationMembers'])->name('members');
                Route::patch('members/{user}', [CommitteePortalController::class, 'updateAssociationMember'])->name('members.update');
            });
        });

    Route::middleware('role:jawatankuasa|pengerusi|setiausaha')
        ->prefix('committee')
        ->name('committee.')
        ->group(function () {
            Route::prefix('associations')->name('associations.')->group(function () {
                Route::get('approvals', [CommitteePortalController::class, 'associationApprovals'])->name('approvals');
            });
        });

    Route::middleware('role:jawatankuasa|bendahari')
        ->prefix('committee')
        ->name('committee.')
        ->group(function () {
            Route::prefix('fees')->name('fees.')->group(function () {
                Route::get('settings', [CommitteePortalController::class, 'feeSettings'])->name('settings');
                Route::get('invoices/generate', [CommitteePortalController::class, 'generateInvoices'])->name('invoices.generate');
                Route::get('payments/review', [CommitteePortalController::class, 'reviewPayments'])->name('payments.review');
                Route::get('arrears', [CommitteePortalController::class, 'arrears'])->name('arrears');
            });
        });

    Route::middleware('role:super_admin')
        ->prefix('committee')
        ->name('committee.')
        ->group(function () {
            Route::get('associations/create', [CommitteeAssociationController::class, 'create'])->name('associations.create');
            Route::post('associations', [CommitteeAssociationController::class, 'store'])->name('associations.store');
            Route::get('associations/{association}/edit', [CommitteeAssociationController::class, 'edit'])->name('associations.edit');
            Route::put('associations/{association}', [CommitteeAssociationController::class, 'update'])->name('associations.update');
            Route::delete('associations/{association}', [CommitteeAssociationController::class, 'destroy'])->name('associations.destroy');
        });
});

require __DIR__.'/auth.php';
