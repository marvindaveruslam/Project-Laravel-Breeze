<?php

namespace App\Http\Controllers;

use App\Models\KelasSiswa;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SiswaController extends Controller
{
    public function index()
    {
        // Mengambil semua data siswa beserta nama kelasnya (pakai with)
        $siswa = Siswa::with('kelas')
            ->orderBy('nama')
            ->get();

        // Menghitung statistik (seperti di KelasController)
        $totalSiswa = Siswa::count();
        $totalKelas = Kelas::count();

        // WAJIB pakai Inertia::render, BUKAN return view()
        return Inertia::render('Kelas/Siswa/Index', [
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
        // Kirim data kelas untuk dropdown pilihan kelas di frontend
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();

        return Inertia::render('Kelas/Siswa/Create', [
            'kelas' => $kelas
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nis' => 'required|string|max:20|unique:siswas,nis',
            'kelas_id' => 'required|exists:kelas,id', // Validasi ID kelas
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:15',
        ]);

        Siswa::create($validated);

        // Redirect pakai route (bukan url), dengan flash message
        return redirect()
            ->route('kelas.siswa')
            ->with('success', 'Data siswa berhasil ditambahkan!');
    }

    public function show(Siswa $siswa)
    {
        return Inertia::render('Kelas/Siswa/Show', [
            'siswa' => $siswa
        ]);
    }

    public function edit(Siswa $siswa)
    {
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();

        return Inertia::render('Kelas/Siswa/Edit', [
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
            ->route('kelas.siswa')
            ->with('success', 'Data siswa berhasil diperbarui!');
    }

    public function destroy(Siswa $siswa)
    {
        // (Opsional) Cek jika siswa punya relasi nilai/absensi, cegah hapus
        // if ($siswa->nilais()->count() > 0) {
        //     return redirect()->route('kelas.siswa')->with('error', 'Tidak bisa dihapus karena masih ada data nilai.');
        // }

        $siswa->delete();

        return redirect()
            ->route('kelas.siswa')
            ->with('success', 'Data siswa berhasil dihapus!');
    }
}