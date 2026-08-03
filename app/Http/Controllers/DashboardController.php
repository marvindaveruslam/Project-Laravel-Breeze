<?php

namespace App\Http\Controllers;

use App\Models\Santri;
use App\Models\Kelas;
use App\Models\Guru;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display dashboard utama
     */
    public function index(Request $request): Response
    {
        // Data Santri
        $santris = Santri::with('kelas')->latest()->get();
        $totalSantri = $santris->count();
        $lakiLaki = $santris->where('jenis_kelamin', '1')->count();
        $perempuan = $santris->where('jenis_kelamin', '2')->count();

        // Data Guru
        $gurus = Guru::latest()->get();
        $totalGuru = $gurus->count();

        // Data Kelas
        $kelas = Kelas::withCount('santris')
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();
        $totalKelas = $kelas->count();

        // Data Absensi Hari Ini
        $totalAbsensiHariIni = Absensi::whereDate('tanggal', today())->count();

        $hadir = Absensi::whereDate('tanggal', today())
            ->where('status', 'hadir')
            ->count();

        $izin = Absensi::whereDate('tanggal', today())
            ->where('status', 'izin')
            ->count();

        $sakit = Absensi::whereDate('tanggal', today())
            ->where('status', 'sakit')
            ->count();

        $alpha = Absensi::whereDate('tanggal', today())
            ->where('status', 'alpha')
            ->count();

        $persentaseHadir = $totalSantri > 0
        ? round(($hadir / $totalSantri) * 100, 2)
        : 0;
        // Aktivitas Terbaru (gabungan dari santri dan guru)
        $recentActivities = collect();

        // Aktivitas dari Santri (5 terbaru)
        $santris->take(5)->each(function($s) use (&$recentActivities) {
            $recentActivities->push([
                'type' => 'santri',
                'name' => $s->nama,
                'class' => $s->kelas->nama_kelas ?? '-',
                'action' => 'Santri terdaftar',
                'time' => $s->created_at->diffForHumans(),
                'status' => 'success'
            ]);
        });

        // Aktivitas dari Guru (5 terbaru)
        $gurus->take(5)->each(function($g) use (&$recentActivities) {
            $recentActivities->push([
                'type' => 'guru',
                'name' => $g->nama,
                'class' => $g->mata_pelajaran ?? '-',
                'action' => 'Guru terdaftar',
                'time' => $g->created_at->diffForHumans(),
                'status' => 'info'
            ]);
        });

        // Sort by time (terbaru) dan ambil 10
        $recentActivities = $recentActivities->sortByDesc('time')->take(10)->values();

        return Inertia::render('Dashboard', [
            'santris' => $santris,
            'gurus' => $gurus,
            'kelas' => $kelas,
            'stats' => [
                'total_santri' => $totalSantri,
                'laki_laki' => $lakiLaki,
                'perempuan' => $perempuan,
                'total_guru' => $totalGuru,
                'total_kelas' => $totalKelas,

                'absensi_hari_ini' => $totalAbsensiHariIni,
                'hadir' => $hadir,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpha' => $alpha,
                'persentase_hadir' => $persentaseHadir,
            ],
            'recent_activities' => $recentActivities,
        ]);
    }

    /**
     * Halaman Data Santri
     */
    public function santri(Request $request): Response
    {
        $santris = Santri::with('kelas')->latest()->get();
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();

        return Inertia::render('Dashboard/Santri/Index', [
            'santris' => $santris,
            'kelas' => $kelas,
        ]);
    }

    /**
     * Halaman Data Guru
     */
    public function guru(Request $request): Response
    {
        $gurus = Guru::latest()->get();

        return Inertia::render('Dashboard/Guru/Index', [
            'gurus' => $gurus,
        ]);
    }

    /**
     * Halaman Data Kelas
     */
    public function classes(Request $request): Response
    {
        $kelas = Kelas::withCount('santris')
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        $totalKelas = $kelas->count();
        $totalSantri = Santri::count();
        $tingkat = $kelas->groupBy('tingkat')->count();

        return Inertia::render('Dashboard/Classes/SingleClass', [
            'kelas' => $kelas,
            'statistik' => [
                'total_kelas' => $totalKelas,
                'total_santri' => $totalSantri,
                'rata_rata' => $totalKelas > 0 ? round($totalSantri / $totalKelas) : 0,
                'tingkat' => $tingkat,
            ]
        ]);
    }

    /**
     * Halaman Data absensi
     */
    public function absensi(Request $request): Response
    {
        $absensis = Absensi::with(['santri', 'kelas', 'guru'])
            ->latest()
            ->paginate(10);

        $kelas = Kelas::orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        $gurus = Guru::orderBy('nama')->get();

        return Inertia::render('Dashboard/Absensi/Index', [
            'absensis' => $absensis,
            'kelas' => $kelas,
            'gurus' => $gurus,
        ]);
    }

    /**
     * Halaman Keuangan
     */
    public function finance(Request $request): Response
    {
        return Inertia::render('Dashboard/Finance/GeneralFinance');
    }
}
