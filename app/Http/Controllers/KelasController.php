<?php

namespace App\Http\Controllers;

use App\Models\KelasSantri;
use App\Models\KelasGuru;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KelasController extends Controller
{
    public function index()
    {
        $santri = Santri::with('kelas')
            ->orderBy('nama')
            ->get();

        $totalSantri = Santri::count();
        $totalKelas = Kelas::count();

        return Inertia::render('Dashboard/Santri/Index', [
            'santri' => $santri,
            'stats' => [
                'total_santri' => $totalSantri,
                'total_kelas' => $totalKelas,
                'rata_rata_santri' => $totalKelas > 0 ? round($totalSantri / $totalKelas, 1) : 0,
            ]
        ]);
    }

    public function create()
    {
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();

        return Inertia::render('Dashboard/Santri/Create', [
            'kelas' => $kelas
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nis' => 'required|string|max:20|unique:santris,nis',
            'kelas_id' => 'required|exists:kelas,id',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:15',
        ]);

        Santri::create($validated);

        return redirect()
            ->route('dashboard.santri')
            ->with('success', 'Data santri berhasil ditambahkan.');
    }

    public function edit(Santri $santri)
    {
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();

        return Inertia::render('Dashboard/Santri/Edit', [
            'santri' => $santri,
            'kelas' => $kelas
        ]);
    }

    public function update(Request $request, Santri $santri)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nis' => 'required|string|max:20|unique:santris,nis,' . $santri->id,
            'kelas_id' => 'required|exists:kelas,id',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:15',
        ]);

        $santri->update($validated);

        return redirect()
            ->route('dashboard.santri')
            ->with('success', 'Data santri berhasil diperbarui.');
    }

    public function destroy(Santri $santri)
    {
        $santri->delete();

        return redirect()
            ->route('dashboard.santri')
            ->with('success', 'Data santri berhasil dihapus.');
    }
}
