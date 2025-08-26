<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PasienController;
use App\Http\Controllers\Admin\MonitoringController;
use App\Http\Controllers\Admin\NotifikasiController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\PengaturanController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\ProfilController;
use App\Http\Controllers\User\MonitoringController as UserMonitoringController;
use App\Http\Controllers\QrCodeController;

// Test route untuk debugging
Route::get('/test-user-dashboard', [UserDashboardController::class, 'index'])->name('test.user.dashboard');

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard.explicit');
    Route::get('/pasien', [PasienController::class, 'index'])->name('admin.pasien');
    Route::get('/monitoring', [MonitoringController::class, 'index'])->name('admin.monitoring');
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('admin.notifikasi');
    Route::post('/notifikasi', [NotifikasiController::class, 'store'])->name('admin.notifikasi.store');
    Route::get('/backup', [BackupController::class, 'index'])->name('admin.backup');
    Route::post('/backup', [BackupController::class, 'store'])->name('admin.backup.store');
    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('admin.pengaturan');
    Route::post('/pengaturan', [PengaturanController::class, 'update'])->name('admin.pengaturan.update');
});

// User routes
Route::middleware(['auth', 'user'])->prefix('user')->group(function () {
    Route::get('/', [UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard.explicit');
    Route::get('/profil', [ProfilController::class, 'index'])->name('user.profil');
    Route::post('/profil', [ProfilController::class, 'update'])->name('user.profil.update');
    Route::get('/monitoring', [UserMonitoringController::class, 'index'])->name('user.monitoring');
    Route::post('/monitoring', [UserMonitoringController::class, 'store'])->name('user.monitoring.store');
    Route::get('/monitoring/chart-data', [UserMonitoringController::class, 'getChartDataApi'])->name('user.monitoring.chart-data');
    
    // QR Code routes (hanya untuk generate dan download)
    Route::get('/qr-code/generate', [QrCodeController::class, 'generateProfileQrCode'])->name('user.qr-code.generate');
    Route::get('/qr-code/download', [QrCodeController::class, 'downloadProfileQrCode'])->name('user.qr-code.download');
});

// Public profile page untuk QR code (bisa diakses tanpa login)
Route::get('/profile/{user_id}', [QrCodeController::class, 'showPublicProfile'])->name('user.public.profile');

// Default route
Route::get('/', function () {
    return redirect()->route('login');
});
