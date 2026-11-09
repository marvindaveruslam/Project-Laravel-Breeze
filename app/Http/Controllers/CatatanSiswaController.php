<?php

namespace App\Http\Controllers;

use App\Models\CatatanSiswa; // Pastikan model ini ada
use App\Models\Siswa;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CatatanSiswaController extends Controller
{
    // Untuk menampilkan riwayat izin, sakit, alpha, pelanggaran
    public function index()
    {
        // Ambil data catatan beserta data siswanya (relasi)
        $catatans = CatatanSiswa::with('siswa')->latest()->get();
        $siswas = Siswa::all(); // Untuk dropdown pilih siswa di form

        return Inertia::render('Dashboard/Siswa/Catatan', [
            'catatans' => $catatans,
            'siswas' => $siswas
        ]);
    }

    // Untuk menyimpan catatan baru (izin/sakit/alpha/pelanggaran)
    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'jenis' => 'required|in:izin,sakit,alpha,pelanggaran',
            'tanggal' => 'required|date',
            'keterangan' => 'required|string',
        ]);

        CatatanSiswa::create($request->all());

        return redirect()->back()->with('success', 'Catatan berhasil disimpan!');
    }
}