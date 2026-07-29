<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Kelas;
use App\Models\Santri;
use App\Models\Guru;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('Dashboard');
    }

    public function classes(Request $request): Response
    {
        // Ambil data kelas dengan jumlah santri
        $kelas = Kelas::withCount('santris')
            ->latest()
            ->get();

        // Hitung statistik
        $totalKelas = $kelas->count();
        $totalSantri = Santri::count();
        $rataRata = $totalKelas > 0 ? round($totalSantri / $totalKelas) : 0;

        // Hitung jumlah tingkat yang berbeda
        $tingkat = $kelas->groupBy('tingkat')->count();

        return Inertia::render('Dashboard/Classes/SingleClass', [
            'kelas' => $kelas,
            'statistik' => [
                'total_kelas' => $totalKelas,
                'total_santri' => $totalSantri,
                'rata_rata' => $rataRata,
                'tingkat' => $tingkat,
            ]
        ]);
    }

    /**
     * 📚 Halaman Data Santri
     * Menampilkan semua data santri dengan filter kelas
     */
    public function santri(Request $request): Response
    {
        // Ambil semua data santri dengan relasi kelas
        $santri = Santri::with('kelas')
            ->latest()
            ->get();

        // Ambil data kelas untuk filter
        $kelas = Kelas::all();

        // Hitung statistik santri
        $totalSantri = Santri::count();
        $totalKelas = Kelas::count();
        $rataRata = $totalKelas > 0 ? round($totalSantri / $totalKelas) : 0;

        return Inertia::render('Dashboard/Santri/DataSantri', [
            'santri' => $santri,
            'kelas' => $kelas,
            'statistik' => [
                'total_santri' => $totalSantri,
                'total_kelas' => $totalKelas,
                'rata_rata' => $rataRata,
            ]
        ]);
    }

    /**
     * 👨‍🏫 Halaman Data Guru
     * Menampilkan semua data guru dengan filter kelas
     */
    public function guru(Request $request): Response
    {
        // Ambil semua data guru dengan relasi kelas
        $guru = Guru::with('kelas')
            ->latest()
            ->get();

        // Ambil data kelas untuk filter
        $kelas = Kelas::all();

        // Hitung statistik guru
        $totalGuru = Guru::count();
        $totalKelas = Kelas::count();
        $rataRata = $totalKelas > 0 ? round($totalGuru / $totalKelas) : 0;

        return Inertia::render('Dashboard/Guru/DataGuru', [
            'guru' => $guru,
            'kelas' => $kelas,
            'statistik' => [
                'total_guru' => $totalGuru,
                'total_kelas' => $totalKelas,
                'rata_rata' => $rataRata,
            ]
        ]);
    }

    public function finance(Request $request): Response
    {
        return Inertia::render('Dashboard/Finance/GeneralFinance');
    }

}