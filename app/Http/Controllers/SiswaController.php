<?php

namespace App\Http\Controllers;

use App\Models\Student; // Sesuaikan jika nama model Anda 'Santri'
use App\Models\Grade;   // Sesuaikan jika nama model nilai Anda berbeda
use Illuminate\Http\Request;
use Inertia\Inertia;

class SiswaController extends Controller
{
    /**
     * Menampilkan daftar siswa (Gambar 1)
     * Menggunakan pagination 100 data per halaman sesuai permintaan.
     */
    public function index()
    {
        // Mengambil data siswa dengan pagination 100 baris
        $siswa = Student::paginate(100); 
        
        return Inertia::render('Dashboard/Siswa/Index', [
            'siswa' => $siswa,
        ]);
    }

    /**
     * Menampilkan halaman detail/catatan per 1 siswa (Gambar 2)
     */
    public function show($id)
    {
        // Mengambil data siswa beserta relasi nilai (grades)
        // with('grades') akan memanggil fungsi relasi di Model Student
        $siswa = Student::with('grades')->findOrFail($id);

        return Inertia::render('Dashboard/Siswa/Catatan', [
            'siswa' => $siswa,
        ]);
    }

    /**
     * Memproses update nilai dari halaman catatan (Tombol Simpan Nilai)
     */
    public function updateGrades(Request $request, $id)
    {
        // Validasi input dari frontend
        $request->validate([
            'grades' => 'required|array',
            'grades.*.id' => 'required|exists:grades,id',
            'grades.*.daily_score' => 'nullable|integer|max:100',
            'grades.*.exam_score' => 'nullable|integer|max:100',
        ]);

        $siswa = Student::findOrFail($id);

        // Looping data nilai yang dikirim dari frontend
        foreach ($request->grades as $gradeData) {
            $grade = Grade::where('id', $gradeData['id'])
                           ->where('student_id', $siswa->id)
                           ->first();
            
            if ($grade) {
                // Logika hitung rata-rata otomatis
                $finalScore = null;
                if (isset($gradeData['daily_score']) && isset($gradeData['exam_score'])) {
                    $finalScore = ($gradeData['daily_score'] + $gradeData['exam_score']) / 2;
                }

                // Simpan/Update ke database
                $grade->update([
                    'daily_score' => $gradeData['daily_score'],
                    'exam_score' => $gradeData['exam_score'],
                    'final_score' => $finalScore,
                ]);
            }
        }

        // Kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->back()->with('success', 'Nilai berhasil diperbarui!');
    }
}