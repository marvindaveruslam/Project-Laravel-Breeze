<?php

namespace App\Http\Controllers;

use App\Models\Santri;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SantriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $santris = Santri::with('kelas')
            ->latest()
            ->get();

        // ✅ AMBIL DATA KELAS UNTUK FILTER DAN DROPDOWN
        $kelas = Kelas::orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        return Inertia::render('Dashboard/Santri/Index', [
            'santris' => $santris,
            'kelas' => $kelas, // <-- INI YANG HARUS DITAMBAHKAN!
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kelas = Kelas::orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        return Inertia::render('Dashboard/Santri/Index', [
            'kelas' => $kelas,
            'showModal' => true, // Flag untuk buka modal otomatis
            'isEditing' => false,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nis'             => 'required|string|max:50|unique:santris,nis',
            'nama'            => 'required|string|max:255',
            'jenis_kelamin'   => 'required|in:1,2',
            'tempat_lahir'    => 'required|string|max:255',
            'tanggal_lahir'   => 'required|date',
            'alamat'          => 'required|string',
            'no_hp'           => 'nullable|string|max:20',
            'kelas_id'        => 'required|exists:kelas,id',
        ]);

        Santri::create($validated);

        return redirect()
            ->route('dashboard.santri')
            ->with('success', 'Data santri berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Santri $santri)
    {
        $santri->load('kelas');

        return Inertia::render('Dashboard/Santri/Show', [
            'santri' => $santri,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Santri $santri)
    {
        $kelas = Kelas::orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        return Inertia::render('Dashboard/Santri/Index', [
            'santri' => $santri,
            'kelas' => $kelas,
            'showModal' => true,
            'isEditing' => true,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Santri $santri)
    {
        $validated = $request->validate([
            'nis'             => 'required|string|max:50|unique:santris,nis,' . $santri->id,
            'nama'            => 'required|string|max:255',
            'jenis_kelamin'   => 'required|in:1,2',
            'tempat_lahir'    => 'required|string|max:255',
            'tanggal_lahir'   => 'required|date',
            'alamat'          => 'required|string',
            'no_hp'           => 'nullable|string|max:20',
            'kelas_id'        => 'required|exists:kelas,id',
        ]);

        $santri->update($validated);

        return redirect()
            ->route('dashboard.santri')
            ->with('success', 'Data santri berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Santri $santri)
    {
        $santri->delete();

        return redirect()
            ->route('dashboard.santri')
            ->with('success', 'Data santri berhasil dihapus.');
    }
}
