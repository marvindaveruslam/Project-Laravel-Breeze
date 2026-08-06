<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\SantriController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\KelasSantriController;
use App\Http\Controllers\KelasGuruController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\MataPelajaranController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// ============================================
// ROUTE UNTUK PROFILE (Auth)
// ============================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ============================================
// ROUTE DASHBOARD (HANYA UNTUK MENAMPILKAN HALAMAN)
// ============================================
Route::middleware('auth')->prefix('dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('finance', [DashboardController::class, 'finance'])->name('dashboard.finance');
    Route::get('classes', [DashboardController::class, 'classes'])->name('dashboard.classes');
    
    // HAPUS route GET yang duplikat dengan resource di bawah
    // Route::get('santri', ...)           → pakai resource /santri
    // Route::get('guru', ...)             → pakai resource /guru
    // Route::get('absensi', ...)          → pakai resource /absensi
    // Route::get('mata-pelajaran', ...)   → pakai resource /mata-pelajaran
    // Route::get('kelas/{kelas}/santri')  → pakai resource /kelas/{kelas}/santri
    // Route::get('kelas/{kelas}/guru')    → pakai resource /kelas/{kelas}/guru
});

// ============================================
// ROUTE CRUD RESOURCE (UNTUK KELOLA DATA)
// ============================================
Route::middleware(['auth'])->group(function () {
    Route::resource('kelas', KelasController::class)->parameters(['kelas' => 'kelas']);
    Route::resource('santri', SantriController::class);
    Route::resource('guru', GuruController::class);
    Route::resource('kelas/{kelas}/santri', KelasSantriController::class);
    Route::resource('kelas/{kelas}/guru', KelasGuruController::class);
    Route::resource('absensi', AbsensiController::class);
    Route::resource('mata-pelajaran', MataPelajaranController::class);
});

require __DIR__.'/auth.php';