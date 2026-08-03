<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    /**
     * Menampilkan halaman form absensi dan riwayat
     */
    public function index()
    {
        // Ambil data absensi user yang sedang login, diurutkan dari tanggal terbaru
        $absensis = Absensi::where('user_id', Auth::id())
                            ->orderBy('tanggal', 'desc')
                            ->paginate(10);

        return view('absensi.index', compact('absensis'));
    }

    /**
     * Proses Absensi Masuk (Clock In)
     */
    public function storeMasuk(Request $request)
    {
        // Cek apakah user sudah absen masuk hari ini
        $today = Carbon::now()->toDateString();
        $existingAbsen = Absensi::where('user_id', Auth::id())
                                 ->where('tanggal', $today)
                                 ->first();

        if ($existingAbsen) {
            return redirect()->back()->with('error', 'Anda sudah melakukan absensi masuk hari ini!');
        }

        // Simpan absensi masuk
        Absensi::create([
            'user_id'    => Auth::id(),
            'tanggal'    => $today,
            'jam_masuk'  => Carbon::now()->toTimeString(),
            'jam_pulang' => null,
        ]);

        return redirect()->back()->with('success', 'Absensi masuk berhasil dicatat!');
    }

    /**
     * Proses Absensi Pulang (Clock Out)
     */
    public function storePulang(Request $request)
    {
        $today = Carbon::now()->toDateString();

        // Cari data absensi hari ini milik user
        $absenHariIni = Absensi::where('user_id', Auth::id())
                                ->where('tanggal', $today)
                                ->first();

        if (!$absenHariIni) {
            return redirect()->back()->with('error', 'Anda belum melakukan absensi masuk hari ini!');
        }

        if ($absenHariIni->jam_pulang) {
            return redirect()->back()->with('error', 'Anda sudah melakukan absensi pulang!');
        }

        // Update jam pulang
        $absenHariIni->update([
            'jam_pulang' => Carbon::now()->toTimeString()
        ]);

        return redirect()->back()->with('success', 'Absensi pulang berhasil dicatat!');
    }

    /**
     * (Opsional) Menampilkan detail riwayat jika diperlukan
     */
    public function show($id)
    {
        $absensi = Absensi::where('id', $id)
                          ->where('user_id', Auth::id())
                          ->firstOrFail();

        return view('absensi.show', compact('absensi'));
    }
}