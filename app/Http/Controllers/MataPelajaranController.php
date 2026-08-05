<?php

namespace App\Http\Controllers;

use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MataPelajaranController extends Controller
{
    /**
     * Menampilkan daftar mata pelajaran.
     */
    public function index()
    {
        $mataPelajarans = MataPelajaran::latest()->get();

        return Inertia::render('Dashboard/MataPelajaran/Index', [
            'mataPelajarans' => $mataPelajarans,
        ]);
    }

    /**
     * Menampilkan form tambah mata pelajaran.
     */
    public function create()
    {
        return Inertia::render('Dashboard/MataPelajaran/Create');
    }

    /**
     * Menyimpan data mata pelajaran.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_mapel' => 'required|string|max:20|unique:mata_pelajarans,kode_mapel',
            'nama_mapel' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        MataPelajaran::create($validated);

        return redirect()->route('mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail mata pelajaran.
     */
    public function show(MataPelajaran $mataPelajaran)
    {
        return Inertia::render('Dashboard/MataPelajaran/Show', [
            'mataPelajaran' => $mataPelajaran,
        ]);
    }

    /**
     * Menampilkan form edit.
     */
    public function edit(MataPelajaran $mataPelajaran)
    {
        return Inertia::render('Dashboard/MataPelajaran/Edit', [
            'mataPelajaran' => $mataPelajaran,
        ]);
    }

    /**
     * Mengupdate data mata pelajaran.
     */
    public function update(Request $request, MataPelajaran $mataPelajaran)
    {
        $validated = $request->validate([
            'kode_mapel' => 'required|string|max:20|unique:mata_pelajarans,kode_mapel,' . $mataPelajaran->id,
            'nama_mapel' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        $mataPelajaran->update($validated);

        return redirect()->route('mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    /**
     * Menghapus data mata pelajaran.
     */
    public function destroy(MataPelajaran $mataPelajaran)
    {
        $mataPelajaran->delete();

        return redirect()->route('mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}
