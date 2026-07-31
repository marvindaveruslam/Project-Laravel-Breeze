<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Santri;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::withCount('santris')
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        // Hitung statistik
        $totalSantri = Santri::count();
        $totalKelas = $kelas->count();

        return Inertia::render('Dashboard/Classes/SingleClass', [
            'kelas' => $kelas,
            'stats' => [
                'total_kelas' => $totalKelas,
                'total_santri' => $totalSantri,
                'rata_rata_santri' => $totalKelas > 0 ? round($totalSantri / $totalKelas, 1) : 0,
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:100',
            'tingkat' => 'required|integer|min:1|max:6',
        ]);

        Kelas::create($validated);

        return redirect()
            ->route('dashboard.classes')
            ->with('success', 'Data kelas berhasil ditambahkan.');
    }

    public function destroy(Kelas $kelas)
    {
        // Cek apakah ada santri di kelas ini
        if ($kelas->santris()->count() > 0) {
            return redirect()
                ->route('dashboard.classes')
                ->with('error', 'Kelas tidak dapat dihapus karena masih memiliki santri.');
        }

        $kelas->delete();

        return redirect()
            ->route('dashboard.classes')
            ->with('success', 'Data kelas berhasil dihapus.');
    }
}
