<?php

namespace App\Http\Controllers;

use App\Models\Siswa; // Pastikan model Siswa sudah dibuat
use Illuminate\Http\Request;
use Inertia\Inertia;

class SiswaController extends Controller
{
    // Untuk menampilkan halaman daftar siswa
    public function index()
    {
        $siswas = Siswa::latest()->get();
        
        // Karena pakai Inertia, kita render ke file React/Vue
        return Inertia::render('Dashboard/Siswa/Index', [
            'siswas' => $siswas
        ]);
    }

    // Untuk menyimpan data siswa baru (jika ada form tambah siswa)
    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|unique:siswas',
            'nama_lengkap' => 'required',
            'jenis_kelamin' => 'required|in:L,P',
            'kelas' => 'required',
        ]);

        Siswa::create($request->all());

        return redirect()->back()->with('success', 'Data siswa berhasil ditambahkan!');
    }
}