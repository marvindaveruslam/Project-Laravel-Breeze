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
use App\Http\Controllers\SiswaController; 
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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ==========================================
// RUTE DASHBOARD (PREFIX: /dashboard)
// ==========================================
Route::middleware('auth')->prefix('dashboard')->group(function () {

    // --- Hanya admin & operator ---
    Route::middleware('role:admin,operator')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
        Route::get('finance', [DashboardController::class, 'finance'])->name('dashboard.finance');
        Route::get('santri', [DashboardController::class, 'santri'])->name('dashboard.santri');
        Route::get('guru', [DashboardController::class, 'guru'])->name('dashboard.guru');
        Route::get('absensi', [DashboardController::class, 'absensi'])->name('dashboard.absensi');
        Route::get('mata-pelajaran', [DashboardController::class, 'mataPelajaran'])->name('dashboard.mata-pelajaran');

        // --- MODUL KELAS DI DASHBOARD ---
        Route::get('classes', [DashboardController::class, 'classes'])->name('dashboard.classes');
        Route::get('classes/{kelas}/detail', [KelasController::class, 'show'])->name('dashboard.detail');
        Route::post('classes/{kelas}/guru', [KelasController::class, 'storeGuru'])->name('classes.storeGuru');
        Route::delete('classes/{kelas}/guru/{guruId}', [KelasController::class, 'destroyGuru'])->name('classes.destroyGuru');
        Route::put('classes/{kelas}/walikelas', [KelasController::class, 'updateWalikelas'])->name('classes.updateWalikelas');
        Route::put('classes/{kelas}/guru/{guruId}', [KelasController::class, 'updateGuru'])->name('classes.updateGuru');
    });

    // --- MODUL SISWA: semua peran, tapi dibatasi per siswa di SiswaController (Policy) ---
    Route::middleware('role:admin,operator,walikelas,guru')->group(function () {
        Route::get('siswa', [SiswaController::class, 'index'])->name('dashboard.siswa');
        Route::post('siswa', [SiswaController::class, 'store'])->name('dashboard.siswa.store');
        Route::get('siswa/{id}', [SiswaController::class, 'show'])->whereNumber('id')->name('dashboard.siswa.show');
        Route::put('siswa/{id}', [SiswaController::class, 'update'])->whereNumber('id')->name('dashboard.siswa.update');
        Route::get('siswa/{id}/nilai', [SiswaController::class, 'nilai'])->whereNumber('id')->name('dashboard.siswa.nilai');
        Route::put('siswa/{id}/nilai', [SiswaController::class, 'updateGrades'])->whereNumber('id')->name('dashboard.siswa.grades.update');
        Route::delete('siswa/{id}/nilai/{gradeId}', [SiswaController::class, 'destroyGrade'])->whereNumber(['id', 'gradeId'])->name('dashboard.siswa.grades.destroy');
        Route::post('siswa/{id}/kehadiran', [SiswaController::class, 'storeAttendance'])->whereNumber('id')->name('dashboard.siswa.attendance.store');
        Route::delete('siswa/{id}/kehadiran/{attendanceId}', [SiswaController::class, 'destroyAttendance'])->whereNumber(['id', 'attendanceId'])->name('dashboard.siswa.attendance.destroy');
    });
});

// ==========================================
// RUTE CRUD RESOURCE (Tanpa Prefix Dashboard) — hanya admin & operator
// ==========================================
Route::middleware(['auth', 'role:admin,operator'])->group(function () {
    Route::resource('kelas', KelasController::class)->parameters(['kelas' => 'kelas']);
    Route::resource('santri', SantriController::class);
    Route::resource('guru', GuruController::class);
    Route::resource('absensi', AbsensiController::class);
    Route::resource('mata-pelajaran', MataPelajaranController::class);

    // Rute Nested Resource
    Route::resource('kelas/{kelas}/santri', KelasSantriController::class);
    Route::resource('kelas/{kelas}/guru', KelasGuruController::class);
});

require __DIR__.'/auth.php';