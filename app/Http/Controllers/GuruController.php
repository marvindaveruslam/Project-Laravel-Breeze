<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GuruController extends Controller
{
    /**
     * Menampilkan daftar guru
     */
    public function index()
    {
        $gurus = Guru::latest()->get();

        return Inertia::render('Guru/Index', [
            'gurus' => $gurus,
        ]);
    }

    /**
     * Menampilkan form tambah guru
     */
    public function create()
    {
        return Inertia::render('Guru/Create');
    }

    /**
     * Menyimpan data guru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip' => 'required|string|max:30|unique:gurus,nip',
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:1,2',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'mata_pelajaran' => 'required|string|max:100',
            'email' => 'nullable|email|unique:gurus,email',
        ]);

        Guru::create($validated);

        return redirect()
            ->route('guru.index')
            ->with('success', 'Data guru berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail guru
     */
    public function show(Guru $guru)
    {
        return Inertia::render('Guru/Show', [
            'guru' => $guru,
        ]);
    }

    /**
     * Menampilkan form edit guru
     */
    public function edit(Guru $guru)
    {
        return Inertia::render('Guru/Edit', [
            'guru' => $guru,
        ]);
    }

    /**
     * Mengupdate data guru
     */
    public function update(Request $request, Guru $guru)
    {
        $validated = $request->validate([
            'nip' => 'required|string|max:30|unique:gurus,nip,' . $guru->id,
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:1,2',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'mata_pelajaran' => 'required|string|max:100',
            'email' => 'nullable|email|unique:gurus,email,' . $guru->id,
        ]);

        $guru->update($validated);

        return redirect()
            ->route('guru.index')
            ->with('success', 'Data guru berhasil diperbarui.');
    }

    /**
     * Menghapus data guru
     */
    public function destroy(Guru $guru)
    {
        $guru->delete();

        return redirect()
            ->route('guru.index')
            ->with('success', 'Data guru berhasil dihapus.');
    }
}
