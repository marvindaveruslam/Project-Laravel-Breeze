<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa; // Pastikan kamu sudah punya Model Siswa

class SiswaController extends Controller
{
    /**
     * Menampilkan daftar semua siswa (Read)
     */
    public function index()
    {
        // Mengambil semua data siswa dari database
        $siswa = Siswa::all();
        
        // Mengirim data ke view (misal: resources/views/siswa/index.blade.php)
        return view('siswa.index', compact('siswa'));
    }

  
    public function create()
    {
        return view('siswa.create');
    }

    
    public function store(Request $request)
    {
      
        $request->validate([
            'nama' => 'required|string|max:255',
            'nis' => 'required|string|max:20|unique:siswas,nis', 
            'kelas' => 'required|string|max:50',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:15',
        ]);

      
        Siswa::create([
            'nama' => $request->nama,
            'nis' => $request->nis,
            'kelas' => $request->kelas,
            'alamat' => $request->alamat,
            'no_hp' => $request->no_hp,
        ]);

        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil ditambahkan!');
    }

   
    public function show($id)
    {
        $siswa = Siswa::findOrFail($id);
        return view('siswa.show', compact('siswa'));
    }

    /**
     * Menampilkan form untuk mengedit data siswa (Update - Form)
     */
    public function edit($id)
    {
        $siswa = Siswa::findOrFail($id);
        return view('siswa.edit', compact('siswa'));
    }

    /**
     * Memperbarui data siswa di database (Update - Update)
     */
    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        // 1. Validasi (NIS tidak perlu unique jika milik siswa itu sendiri)
        $request->validate([
            'nama' => 'required|string|max:255',
            'nis' => 'required|string|max:20|unique:siswas,nis,' . $siswa->id,
            'kelas' => 'required|string|max:50',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:15',
        ]);

        // 2. Update data
        $siswa->update([
            'nama' => $request->nama,
            'nis' => $request->nis,
            'kelas' => $request->kelas,
            'alamat' => $request->alamat,
            'no_hp' => $request->no_hp,
        ]);

        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil diperbarui!');
    }

    /**
     * Menghapus data siswa dari database (Delete)
     */
    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        $siswa->delete();

        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil dihapus!');
    }
}