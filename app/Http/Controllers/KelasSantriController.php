<?php

namespace App\Http\Controllers;

use App\Models\KelasSantri;
use App\Models\Kelas;
use App\Models\Santri; // <-- TAMBAHKAN INI
use Illuminate\Http\Request;
use Inertia\Inertia;

class KelasSantriController extends Controller
{
    public function index()
    {
        // Mengambil semua data santri beserta nama kelasnya (pakai with)
        $santri = Santri::with('kelas')
            ->orderBy('nama')
            ->get();

        // Menghitung statistik (seperti di KelasController)
        $totalSantri = Santri::count();
        $totalKelas = Kelas::count();

        // WAJIB pakai Inertia::render, BUKAN return view()
        return Inertia::render('Kelas/Santri/Index', [
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
        // Kirim data kelas untuk dropdown pilihan kelas di frontend
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();

        return Inertia::render('Kelas/Santri/Create', [
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
            ->route('kelas.santri')
            ->with('success', 'Data santri berhasil ditambahkan!');
    }

    public function show(Santri $santri)
    {
        return Inertia::render('Kelas/Santri/Show', [
            'santri' => $santri
        ]);
    }

    public function edit(Santri $santri)
    {
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();

        return Inertia::render('Kelas/Santri/Edit', [
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
            ->route('kelas.santri')
            ->with('success', 'Data santri berhasil diperbarui!');
    }

    public function destroy(Santri $santri)
    {
        // (Opsional) Cek jika santri punya relasi nilai/absensi, cegah hapus
        // if ($santri->nilais()->count() > 0) {
        //     return redirect()->route('kelas.santri')->with('error', 'Tidak bisa dihapus karena masih ada data nilai.');
        // }

        $santri->delete();

        return redirect()
            ->route('kelas.santri')
            ->with('success', 'Data santri berhasil dihapus!');
    }
}
