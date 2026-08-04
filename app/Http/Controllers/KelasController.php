<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SiswaController extends Controller
{
    public function index()
    {
        $siswa = Siswa::with('kelas')
            ->orderBy('nama')
            ->get();

        $totalSiswa = Siswa::count();
        $totalKelas = Kelas::count();

        return Inertia::render('Dashboard/Siswa/Index', [
            'siswa' => $siswa,
            'stats' => [
                'total_siswa' => $totalSiswa,
                'total_kelas' => $totalKelas,
                'rata_rata_siswa' => $totalKelas > 0 ? round($totalSiswa / $totalKelas, 1) : 0,
            ]
        ]);
    }

    public function create()
    {
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();

        return Inertia::render('Dashboard/Siswa/Create', [
            'kelas' => $kelas
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nis' => 'required|string|max:20|unique:siswas,nis',
            'kelas_id' => 'required|exists:kelas,id',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:15',
        ]);

        Siswa::create($validated);

        return redirect()
            ->route('dashboard.siswa')
            ->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function edit(Siswa $siswa)
    {
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();

        return Inertia::render('Dashboard/Siswa/Edit', [
            'siswa' => $siswa,
            'kelas' => $kelas
        ]);
    }

    public function update(Request $request, Siswa $siswa)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nis' => 'required|string|max:20|unique:siswas,nis,' . $siswa->id,
            'kelas_id' => 'required|exists:kelas,id',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:15',
        ]);

        $siswa->update($validated);

        return redirect()
            ->route('dashboard.siswa')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Siswa $siswa)
    {
        $siswa->delete();

        return redirect()
            ->route('dashboard.siswa')
            ->with('success', 'Data siswa berhasil dihapus.');
    }
}