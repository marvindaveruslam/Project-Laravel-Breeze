<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Student;
use App\Models\StudentAttendance;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class SiswaController extends Controller
{
    /**
     * Daftar siswa dengan pencarian + pagination (10 baris/halaman).
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $siswas = Student::query()
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($w) use ($search) {
                    $w->where('name', 'like', "%{$search}%")
                      ->orWhere('nis', 'like', "%{$search}%")
                      ->orWhere('class', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(10) // <-- DIUBAH DARI 100 MENJADI 10
            ->withQueryString();

        return Inertia::render('Dashboard/Siswa/Index', [
            'siswas'  => $siswas,
            'filters' => ['search' => $search],
            'stats'   => [
                'total'      => Student::count(),
                'laki_laki'  => Student::where('gender', 'L')->count(),
                'perempuan'  => Student::where('gender', 'P')->count(),
            ],
        ]);
    }

    /**
     * Tambah siswa baru (dari modal di halaman daftar).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nis'              => 'required|string|max:50|unique:students,nis',
            'name'             => 'required|string|max:255',
            'gender'           => ['required', Rule::in(['L', 'P'])],
            'class'            => 'nullable|string|max:50',
            'homeroom_teacher' => 'nullable|string|max:255',
        ]);

        $student = Student::create($data);

        return redirect()
            ->route('dashboard.siswa.show', $student->id)
            ->with('success', 'Siswa berhasil ditambahkan.');
    }

    /**
     * Landing page individu siswa: biodata + nilai + kehadiran + walikelas.
     */
    public function show($id)
    {
        $siswa = Student::with(['grades' => fn ($q) => $q->orderBy('subject')])->findOrFail($id);

        $counts = $siswa->attendances()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $attendanceSummary = [];
        foreach (StudentAttendance::STATUSES as $status) {
            $attendanceSummary[$status] = (int) ($counts[$status] ?? 0);
        }

        $recentAttendances = $siswa->attendances()
            ->orderByDesc('date')
            ->limit(15)
            ->get(['id', 'date', 'status', 'note']);

        return Inertia::render('Dashboard/Siswa/Show', [
            'siswa'             => $siswa,
            'attendanceSummary' => $attendanceSummary,
            'recentAttendances' => $recentAttendances,
        ]);
    }

    /**
     * Update biodata (termasuk nama walikelas).
     */
    public function update(Request $request, $id)
    {
        $siswa = Student::findOrFail($id);

        $data = $request->validate([
            'nis'              => ['required', 'string', 'max:50', Rule::unique('students', 'nis')->ignore($siswa->id)],
            'name'             => 'required|string|max:255',
            'gender'           => ['required', Rule::in(['L', 'P'])],
            'class'            => 'nullable|string|max:50',
            'birth_place'      => 'nullable|string|max:255',
            'birth_date'       => 'nullable|date',
            'parent_name'      => 'nullable|string|max:255',
            'parent_phone'     => 'nullable|string|max:30',
            'dormitory'        => 'nullable|string|max:100',
            'address'          => 'nullable|string',
            'homeroom_teacher' => 'nullable|string|max:255',
            'status'           => ['required', Rule::in(['Aktif', 'Lulus', 'Keluar'])],
        ]);

        $siswa->update($data);

        return back()->with('success', 'Biodata siswa berhasil diperbarui.');
    }

    /**
     * Simpan / perbarui semua nilai siswa sekaligus dari satu halaman.
     * Baris tanpa "id" dianggap mata pelajaran baru.
     */
    public function updateGrades(Request $request, $id)
    {
        $siswa = Student::findOrFail($id);

        $request->validate([
            'grades'                => 'required|array|min:1',
            'grades.*.id'           => 'nullable|integer',
            'grades.*.subject'      => 'required|string|max:255|distinct',
            'grades.*.daily_score'  => 'nullable|integer|min:0|max:100',
            'grades.*.exam_score'   => 'nullable|integer|min:0|max:100',
        ]);

        foreach ($request->grades as $row) {
            $daily = $row['daily_score'] ?? null;
            $exam  = $row['exam_score'] ?? null;

            // Rata-rata: jika hanya salah satu terisi, pakai yang terisi
            $scores = array_filter([$daily, $exam], fn ($v) => $v !== null && $v !== '');
            $final  = count($scores) ? round(array_sum($scores) / count($scores)) : null;

            $payload = [
                'subject'     => $row['subject'],
                'daily_score' => $daily,
                'exam_score'  => $exam,
                'final_score' => $final,
            ];

            if (!empty($row['id'])) {
                Grade::where('id', $row['id'])
                    ->where('student_id', $siswa->id)
                    ->update($payload);
            } else {
                Grade::updateOrCreate(
                    ['student_id' => $siswa->id, 'subject' => $row['subject']],
                    $payload
                );
            }
        }

        return back()->with('success', 'Nilai berhasil disimpan.');
    }

    public function destroyGrade($id, $gradeId)
    {
        Grade::where('id', $gradeId)->where('student_id', $id)->delete();

        return back()->with('success', 'Mata pelajaran dihapus.');
    }

    /**
     * Catat kehadiran (hadir/izin/sakit/alpa) untuk satu tanggal.
     * Jika tanggal sudah ada, catatan diperbarui.
     */
    public function storeAttendance(Request $request, $id)
    {
        $siswa = Student::findOrFail($id);

        $data = $request->validate([
            'date'   => 'required|date',
            'status' => ['required', Rule::in(StudentAttendance::STATUSES)],
            'note'   => 'nullable|string|max:255',
        ]);

        StudentAttendance::updateOrCreate(
            ['student_id' => $siswa->id, 'date' => $data['date']],
            ['status' => $data['status'], 'note' => $data['note'] ?? null]
        );

        return back()->with('success', 'Kehadiran berhasil disimpan.');
    }

    public function destroyAttendance($id, $attendanceId)
    {
        StudentAttendance::where('id', $attendanceId)->where('student_id', $id)->delete();

        return back()->with('success', 'Catatan kehadiran dihapus.');
    }
}