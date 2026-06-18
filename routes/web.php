<?php

use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\CaseController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\InventoryItemController as AdminInventoryItemController;
use App\Http\Controllers\Admin\LicenseController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\InventoryItemController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ReviewController;
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

    // Reviews moderation — admin + module:reviews
    Route::middleware('module:reviews')->group(function () {
        Route::get('reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
        Route::patch('reviews/{review}/toggle', [AdminReviewController::class, 'toggleVisibility'])->name('reviews.toggle');
        Route::delete('reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');
    });

    // Analytics — admin + module:analytics
    Route::middleware('module:analytics')->group(function () {
        Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    });
});

// Inventory — gated by module:inventory license
Route::middleware(['auth', 'module:inventory'])->prefix('inventory')->name('inventory.')->group(function () {
    Route::get('/', [InventoryItemController::class, 'index'])->name('index');
    Route::get('/{inventoryItem}', [InventoryItemController::class, 'show'])->name('show');
    Route::post('/{inventoryItem}/usage', [InventoryItemController::class, 'logUsage'])->name('usage.store');
});

// Admin inventory management — admin role + module:inventory
Route::middleware(['auth', 'role:admin', 'module:inventory'])->prefix('admin/inventory')->name('admin.inventory.')->group(function () {
    Route::get('/create', [AdminInventoryItemController::class, 'create'])->name('create');
    Route::post('/', [AdminInventoryItemController::class, 'store'])->name('store');
    Route::get('/{inventoryItem}/edit', [AdminInventoryItemController::class, 'edit'])->name('edit');
    Route::put('/{inventoryItem}', [AdminInventoryItemController::class, 'update'])->name('update');
    Route::post('/{inventoryItem}/restock', [AdminInventoryItemController::class, 'restock'])->name('restock');
    Route::delete('/{inventoryItem}', [AdminInventoryItemController::class, 'destroy'])->name('destroy');
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

    // Reviews — submit review for a work job (module:reviews gated)
    Route::middleware('module:reviews')->group(function () {
        Route::post('work-jobs/{workJob}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    });
});

// Appointments — gated by module:appointments license
Route::middleware(['auth', 'module:appointments'])->group(function () {
    Route::get('/appointments', [AppointmentController::class, 'calendar'])->name('appointments.calendar');
    Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])->name('appointments.show');
    Route::get('/appointments/{appointment}/edit', [AppointmentController::class, 'edit'])->name('appointments.edit');
    Route::put('/appointments/{appointment}', [AppointmentController::class, 'update'])->name('appointments.update');
    Route::patch('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::patch('/appointments/{appointment}/complete', [AppointmentController::class, 'complete'])->name('appointments.complete');

    Route::resource('patients', PatientController::class);
});

// Notifications
Route::middleware('auth')->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::post('/mark-all-read', [NotificationController::class, 'markAllRead'])->name('mark-all-read');
    Route::get('/{id}/read', [NotificationController::class, 'markRead'])->name('read');
});

// Cases — doctors and admins only
Route::middleware(['auth', 'role:doctor,admin'])->group(function () {
    Route::resource('cases', CaseController::class);
});
