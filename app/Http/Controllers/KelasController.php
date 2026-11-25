<?php
// app/Http/Controllers/KelasController.php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Guru;
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
        // Memuat relasi: walikelas, gurus (beserta pivot jadwal), dan santris
        $kelas->load(['walikelas', 'gurus', 'santris']);
        
        // Mengambil daftar semua guru dari tabel 'gurus'
        $daftarGuru = Guru::orderBy('nama')->get();

        return Inertia::render('Dashboard/Classes/DetailClass', [
            'kelas' => $kelas,
            'daftarGuru' => $daftarGuru
        ]);
    }

    // Menyimpan Guru/Jadwal ke Kelas
    public function storeGuru(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'guru_id' => 'required|exists:gurus,id',
            'mata_pelajaran' => 'required|string|max:255',
            'hari' => 'required|string',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
        ]);

        // Simpan ke tabel pivot kelas_guru
        $kelas->gurus()->attach($validated['guru_id'], [
            'mata_pelajaran' => $validated['mata_pelajaran'],
            'hari' => $validated['hari'],
            'jam_mulai' => $validated['jam_mulai'],
            'jam_selesai' => $validated['jam_selesai'],
        ]);

        return back()->with('success', 'Guru dan jadwal berhasil ditambahkan ke kelas.');
    }

    // Mengupdate Jadwal Guru (Dipanggil dari tombol Edit di samping Hapus)
    public function updateGuru(Request $request, Kelas $kelas, $guruId)
    {
        $validated = $request->validate([
            'guru_id' => 'required|exists:gurus,id', // Tambahan: Jika guru diganti
            'mata_pelajaran' => 'required|string|max:255',
            'hari' => 'required|string',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
        ]);

        // Cek apakah relasi guru-kelas ini ada
        $existing = $kelas->gurus()->where('guru_id', $guruId)->first();
        
        if (!$existing) {
            return back()->withErrors(['guru_id' => 'Data jadwal tidak ditemukan.']);
        }

        // Jika guru diganti, kita perlu detach yang lama dan attach yang baru
        if ($guruId != $validated['guru_id']) {
            $kelas->gurus()->detach($guruId);
            $kelas->gurus()->attach($validated['guru_id'], [
                'mata_pelajaran' => $validated['mata_pelajaran'],
                'hari' => $validated['hari'],
                'jam_mulai' => $validated['jam_mulai'],
                'jam_selesai' => $validated['jam_selesai'],
            ]);
        } else {
            // Jika guru tetap sama, cukup update pivotnya
            $kelas->gurus()->updateExistingPivot($guruId, [
                'mata_pelajaran' => $validated['mata_pelajaran'],
                'hari' => $validated['hari'],
                'jam_mulai' => $validated['jam_mulai'],
                'jam_selesai' => $validated['jam_selesai'],
            ]);
        }

        return back()->with('success', 'Jadwal guru berhasil diperbarui.');
    }

    // Menghapus Guru/Jadwal dari Kelas
    public function destroyGuru(Kelas $kelas, $guruId)
    {
        // Menghapus relasi dari tabel pivot kelas_guru
        $kelas->gurus()->detach($guruId);

        return back()->with('success', 'Jadwal guru berhasil dihapus.');
    }

    // Menyimpan / Mengubah Walikelas
    public function updateWalikelas(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'walikelas_id' => 'nullable|exists:gurus,id', // Boleh kosong atau ID guru
        ]);

        $kelas->update([
            'walikelas_id' => $validated['walikelas_id']
        ]);

        return back()->with('success', 'Walikelas berhasil diperbarui.');
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