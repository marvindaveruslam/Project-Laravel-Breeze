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

    public function finance(Request $request): Response
    {
        return Inertia::render('Dashboard/Finance/GeneralFinance');
    }

    public function Santri(Request $request): Response
    {
        return Inertia::render('Dashboard/Santri/Index');
    }


}
