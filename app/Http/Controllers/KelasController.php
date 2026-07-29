<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kelas = Kelas::withCount('santris')
              ->latest()
              ->get();

        return Inertia::render('Dashboard/Classes/SingleClass', [
            'kelas' => $kelas
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Dashboard/Classes/SingleClass', [
            'kelas' => null
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:100',
            'tingkat' => 'required|string|max:50',
        ]);

        Kelas::create($validated);

        // PERBAIKAN: Redirect ke dashboard/classes
        return redirect()
            ->route('dashboard.classes')  // ← ubah ini
            ->with('success', 'Data kelas berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kelas $kelas)
    {
        $kelas->load('santris');

        return Inertia::render('Dashboard/Classes/SingleClass', [
            'kelas' => $kelas,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kelas $kelas)
    {
        return Inertia::render('Dashboard/Classes/SingleClass', [
            'kelas' => $kelas
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:100',
            'tingkat' => 'required|string|max:50',
        ]);

        $kelas->update($validated);

        // PERBAIKAN: Redirect ke dashboard/classes
        return redirect()
            ->route('dashboard.classes')  // ← ubah ini
            ->with('success', 'Data kelas berhasil diubah.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kelas $kelas)
    {
        $kelas->delete();

        // PERBAIKAN: Redirect ke dashboard/classes
        return redirect()
            ->route('dashboard.classes')  // ← ubah ini
            ->with('success', 'Data kelas berhasil dihapus.');
    }
}
