<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\LicenseController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\WorkJobController;
use App\Http\Controllers\WorkJobAttachmentController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

// Locale switcher
Route::get('locale/{locale}', [LocaleController::class, 'set'])->name('locale.set');

// Auth routes (guests only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Role-protected dashboards
Route::middleware('auth')->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/admin',      [DashboardController::class, 'admin'])      ->middleware('role:admin')      ->name('admin');
    Route::get('/doctor',     [DashboardController::class, 'doctor'])     ->middleware('role:doctor')     ->name('doctor');
    Route::get('/technician', [DashboardController::class, 'technician']) ->middleware('role:technician') ->name('technician');
});

// Admin-only management
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserController::class)->only(['index', 'create', 'store', 'edit', 'update']);
    Route::patch('users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');

    Route::get('licenses', [LicenseController::class, 'index'])->name('licenses.index');
    Route::get('licenses/create', [LicenseController::class, 'create'])->name('licenses.create');
    Route::post('licenses', [LicenseController::class, 'store'])->name('licenses.store');
    Route::get('licenses/{license}', [LicenseController::class, 'edit'])->name('licenses.edit');
    Route::put('licenses/{license}', [LicenseController::class, 'update'])->name('licenses.update');
    Route::post('licenses/{license}/toggle-active', [LicenseController::class, 'toggleActive'])->name('licenses.toggle-active');
});

// Work Jobs — accessible to all authenticated users (doctors, technicians, admins)
Route::middleware('auth')->group(function () {
    Route::get('/calendar', [WorkJobController::class, 'calendar'])->name('work-jobs.calendar');
    Route::resource('work-jobs', WorkJobController::class)->except(['index']);

    // Work Job Attachments
    Route::post('work-jobs/{work_job}/attachments', [WorkJobAttachmentController::class, 'store'])
        ->name('work-jobs.attachments.store');
    Route::get('work-jobs/{work_job}/attachments/{attachment}/download', [WorkJobAttachmentController::class, 'download'])
        ->name('work-jobs.attachments.download');
    Route::delete('work-jobs/{work_job}/attachments/{attachment}', [WorkJobAttachmentController::class, 'destroy'])
        ->name('work-jobs.attachments.destroy');
});
