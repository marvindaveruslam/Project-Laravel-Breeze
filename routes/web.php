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
use App\Http\Controllers\SiswaController; // <-- Pastikan ini ada
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

// ==========================================
// ROUTE PROFILE (Bawaan Breeze)
// ==========================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ==========================================
// ROUTE DASHBOARD & FITUR UTAMA
// ==========================================
Route::middleware('auth')->prefix('dashboard')->group(function () {
    
    // --- Dashboard & Halaman Statis Lainnya ---
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('finance', [DashboardController::class, 'finance'])->name('dashboard.finance');
    Route::get('classes', [DashboardController::class, 'classes'])->name('dashboard.classes');
    Route::get('santri', [DashboardController::class, 'santri'])->name('dashboard.santri');
    Route::get('guru', [DashboardController::class, 'guru'])->name('dashboard.guru');
    Route::get('kelas/{kelas}/santri', [DashboardController::class, 'kelasSantri'])->name('dashboard.kelas_santri');
    Route::get('kelas/{kelas}/guru', [DashboardController::class, 'kelasGuru'])->name('dashboard.kelas_guru');
    Route::get('absensi', [DashboardController::class, 'absensi'])->name('dashboard.absensi');
    Route::get('mata-pelajaran', [DashboardController::class, 'mataPelajaran'])->name('dashboard.mata-pelajaran');
    Route::get('classes/{id}/detail', [DashboardController::class, 'detail'])->name('dashboard.detail');

    // --- ROUTE SISWA (Sesuai Langkah Sebelumnya) ---
    // Halaman Daftar Siswa (Gambar 1)
    Route::get('siswa', [SiswaController::class, 'index'])->name('siswa.index');
    
    // Halaman Detail/Catatan Siswa (Gambar 2)
    Route::get('siswa/{siswa}/catatan', [SiswaController::class, 'show'])->name('siswa.catatan');
    
    // Proses Update Nilai dari Halaman Catatan
    Route::put('siswa/{siswa}/nilai', [SiswaController::class, 'updateGrades'])->name('siswa.update-nilai');

    // --- RESOURCE ROUTES (CRUD Bawaan) ---
    // Catatan: Tidak perlu dibungkus Route::middleware('auth')->group lagi karena sudah di dalam grup utama
    Route::resource('kelas', KelasController::class)->parameters(['kelas' => 'kelas']);
    Route::resource('santri', SantriController::class); // Pastikan Anda tahu bedanya 'santri' dan 'siswa' di sistem Anda
    Route::resource('guru', GuruController::class);
    Route::resource('kelas/{kelas}/santri', KelasSantriController::class);
    Route::resource('kelas/{kelas}/guru', KelasGuruController::class);
    Route::resource('absensi', AbsensiController::class);
    Route::resource('mata-pelajaran', MataPelajaranController::class);

});

require __DIR__.'/auth.php';