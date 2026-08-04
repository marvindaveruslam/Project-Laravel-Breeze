<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\SantriController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\KelasSiswaController;
use App\Http\Controllers\AbsensiController;
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


Route::middleware('auth')->prefix('dashboard')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('finance', [DashboardController::class, 'finance'])->name('dashboard.finance');
    Route::get('classes', [DashboardController::class, 'classes'])->name('dashboard.classes');
    Route::get('santri', [DashboardController::class, 'santri'])->name('dashboard.santri');
    Route::get('guru', [DashboardController::class, 'guru'])->name('dashboard.guru');
    Route::get('kelas_siswa', [DashboardController::class, 'kelasSiswa'])->name('dashboard.kelas_siswa');
    Route::get('absensi', [DashboardController::class, 'absensi'])->name('dashboard.absensi');

});


Route::middleware(['auth'])->group(function () {
    Route::resource('kelas', KelasController::class)->parameters(['kelas' => 'kelas']);
    Route::resource('santri', SantriController::class);
    Route::resource('guru', GuruController::class);
    Route::resource('kelas_siswa', KelasSiswaController::class);
    Route::resource('absensi', AbsensiController::class);
    Route::resource('siswa', SiswaController::class)->middleware('auth');
});

require __DIR__.'/auth.php';
