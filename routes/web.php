<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

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
