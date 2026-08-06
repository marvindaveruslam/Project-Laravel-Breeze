<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KelasController extends Controller
{
    // Menampilkan daftar kelas
    public function index()
    {
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        
        return Inertia::render('Dashboard/Classes/SingleClass', [
            'kelas' => $kelas
        ]);
    }

    // Menampilkan form tambah kelas
    public function create()
    {
        return Inertia::render('Dashboard/Classes/SingleClass');
    }

    // Menyimpan data kelas
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'tingkat' => 'required|string|max:50',
            'kode_kelas' => 'nullable|string|max:50|unique:kelas,kode_kelas',
            'deskripsi' => 'nullable|string',
        ]);

        Kelas::create($validated);

        return redirect()
            ->route('dashboard.classes')
            ->with('success', 'Data kelas berhasil ditambahkan.');
    }

    // Menampilkan detail kelas
    public function show(Kelas $kelas)
    {
        return Inertia::render('Dashboard/Classes/SingleClass', [
            'kelas' => $kelas
        ]);
    }

    // Menampilkan form edit kelas
    public function edit(Kelas $kelas)
    {
        return Inertia::render('Dashboard/Classes/SingleClass', [
            'kelas' => $kelas
        ]);
    }

    // Mengupdate data kelas
    public function update(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'tingkat' => 'required|string|max:50',
            'kode_kelas' => 'nullable|string|max:50|unique:kelas,kode_kelas,' . $kelas->id,
            'deskripsi' => 'nullable|string',
        ]);

        $kelas->update($validated);

        return redirect()
            ->route('dashboard.classes')
            ->with('success', 'Data kelas berhasil diperbarui.');
    }

    // Menghapus data kelas
    public function destroy(Kelas $kelas)
    {
        $kelas->delete();

        return redirect()
            ->route('dashboard.classes')
            ->with('success', 'Data kelas berhasil dihapus.');
    }
}