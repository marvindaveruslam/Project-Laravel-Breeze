<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GuruController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $guru = Guru::latest()->get();

    return Inertia::render('Guru/Index', [
        'guru' => $guru,
    ]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Guru/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
        'nama' => 'required|string|max:255',
        'nip' => 'nullable|string|max:30|unique:gurus,nip',
        'jenis_kelamin' => 'required|in:1,2',
        'no_hp' => 'nullable|string|max:20',
        'alamat' => 'nullable|string',
        ]);

        Guru::create($validated);

            return redirect()
                ->route('guru.index')
                ->with('success', 'Guru berhasil ditambahkan.');

    }

    /**
     * Display the specified resource.
     */
    public function show(Guru $guru)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Guru $guru)
    {
        return Inertia::render('Guru/Edit', [
        'guru' => $guru,
    ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Guru $guru)
    {
        $validated = $request->validate([
        'nama' => 'required|string|max:255',
        'nip' => 'nullable|string|max:30|unique:gurus,nip,' . $guru->id,
        'jenis_kelamin' => 'required|in:1,2',
        'no_hp' => 'nullable|string|max:20',
        'alamat' => 'nullable|string',
        ]);

        $guru->update($validated);

        return redirect()
            ->route('guru.index')
            ->with('success', 'Guru berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Guru $guru)
    {
        $guru->delete();

        return redirect()
            ->route('guru.index')
            ->with('success', 'Guru berhasil dihapus.');
    }
}
