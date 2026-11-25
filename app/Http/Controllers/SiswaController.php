<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Kelas;
use App\Models\Student;
use App\Models\StudentAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class SiswaController extends Controller
{
    /** Jumlah baris per halaman. Ubah ke 100 untuk produksi (200-500 siswa). */
    private const PER_PAGE = 10;

    /**
     * Daftar siswa.
     * - admin/operator : semua siswa
     * - walikelas      : siswa kelasnya (detail) + siswa kelas yang ia ajar (hanya input nilai)
     * - guru mapel     : siswa kelas yang ia ajar (hanya nama/NIS/kelas, hanya input nilai)
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Student::class);

        $user = $request->user();
        $search = trim((string) $request->query('search', ''));

        $ownIds = $user->isAdminLike() ? [] : $user->walikelasKelasIds();
        $teachIds = $user->isAdminLike() ? [] : $user->teachingKelasIds();

        $query = Student::query()->with('kelas:id,nama_kelas');

        if (! $user->isAdminLike()) {
            $scope = array_values(array_unique(array_merge($ownIds, $teachIds)));
            $query->whereIn('kelas_id', $scope ?: [0]);
        }

        $query->when($search !== '', function ($q) use ($search) {
            $q->where(function ($w) use ($search) {
                $w->where('name', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%")
                  ->orWhere('class', 'like', "%{$search}%");
            });
        });

        $siswas = $query->orderBy('name')
            ->paginate(self::PER_PAGE)
            ->withQueryString()
            ->through(function (Student $s) use ($user, $ownIds) {
                $full = $user->isAdminLike() || in_array((int) $s->kelas_id, $ownIds, true);

                // Guru mapel HANYA mendapat data minimum
                return [
                    'id'               => $s->id,
                    'nis'              => $s->nis,
                    'name'             => $s->name,
                    'class'            => $s->kelas?->nama_kelas ?? $s->class,
                    'can_detail'       => $full,
                    'gender'           => $full ? $s->gender : null,
                    'homeroom_teacher' => $full ? $s->homeroom_teacher : null,
                    'status'           => $full ? $s->status : null,
                ];
            });

        // Statistik hanya untuk yang berhak melihat data lengkap
        $stats = null;
        if ($user->isAdminLike()) {
            $base = Student::query();
        } elseif ($ownIds) {
            $base = Student::whereIn('kelas_id', $ownIds);
        } else {
            $base = null;
        }
        if ($base) {
            $stats = [
                'total'     => (clone $base)->count(),
                'laki_laki' => (clone $base)->where('gender', 'L')->count(),
                'perempuan' => (clone $base)->where('gender', 'P')->count(),
            ];
        }

        return Inertia::render('Dashboard/Siswa/Index', [
            'siswas'       => $siswas,
            'filters'      => ['search' => $search],
            'stats'        => $stats,
            'kelasOptions' => $user->isAdminLike() ? $this->kelasOptions() : [],
            'access'       => [
                'can_create' => $user->isAdminLike(),
                'mode'       => $user->isAdminLike() ? 'admin' : ($ownIds ? 'walikelas' : 'guru'),
            ],
        ]);
    }

    /**
     * Tambah siswa baru (hanya admin/operator).
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Student::class);

        $data = $request->validate([
            'nis'      => 'required|string|max:50|unique:students,nis',
            'name'     => 'required|string|max:255',
            'gender'   => ['required', Rule::in(['L', 'P'])],
            'kelas_id' => 'nullable|exists:kelas,id',
        ]);

        $this->applyKelas($data);

        $student = Student::create($data);

        return redirect()
            ->route('dashboard.siswa.show', $student->id)
            ->with('success', 'Siswa berhasil ditambahkan.');
    }

    /**
     * Halaman detail lengkap — hanya admin/operator & walikelas kelas tsb.
     * Guru mapel yang mengajar di kelas itu dialihkan ke halaman input nilai.
     */
    public function show(Request $request, $id)
    {
        $siswa = Student::with(['kelas.walikelas:id,nama', 'grades' => fn ($q) => $q->orderBy('subject')])
            ->findOrFail($id);

        if (! Gate::allows('viewDetail', $siswa)) {
            if (Gate::allows('grade', $siswa)) {
                return redirect()->route('dashboard.siswa.nilai', $siswa->id);
            }
            abort(403, 'Anda tidak memiliki akses ke detail siswa ini.');
        }

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

        $user = $request->user();

        // Nama walikelas selalu diambil dari data kelas (bukan teks manual)
        $siswa->setAttribute('class', $siswa->kelas?->nama_kelas ?? $siswa->class);
        $siswa->setAttribute('homeroom_teacher', $siswa->kelas?->walikelas?->nama ?? $siswa->homeroom_teacher);

        return Inertia::render('Dashboard/Siswa/Show', [
            'siswa'             => $siswa,
            'attendanceSummary' => $attendanceSummary,
            'recentAttendances' => $recentAttendances,
            'kelasOptions'      => $user->isAdminLike() ? $this->kelasOptions() : [],
            'access'            => ['can_edit_admin_fields' => $user->isAdminLike()],
        ]);
    }

    /**
     * Halaman input nilai versi terbatas (untuk guru mapel).
     * Hanya memuat nama/NIS/kelas + baris nilai mapel yang boleh dinilai —
     * tidak ada biodata, kehadiran, atau data keuangan.
     */
    public function nilai(Request $request, $id)
    {
        $siswa = Student::with('kelas:id,nama_kelas')->findOrFail($id);
        Gate::authorize('grade', $siswa);

        $allowed = $request->user()->gradableSubjects($siswa); // null = semua
        $existing = $siswa->grades()->orderBy('subject')->get();

        if ($allowed === null) {
            $rows = $existing;
        } else {
            $lower = array_map('mb_strtolower', $allowed);
            $rows = $existing->filter(fn ($g) => in_array(mb_strtolower($g->subject), $lower, true))->values();

            // Mapel yang boleh dinilai tapi belum punya baris -> siapkan baris kosong
            foreach ($allowed as $subject) {
                if (! $rows->contains(fn ($g) => mb_strtolower($g->subject) === mb_strtolower($subject))) {
                    $rows->push(new Grade(['subject' => $subject]));
                }
            }
        }

        return Inertia::render('Dashboard/Siswa/Nilai', [
            'siswa' => [
                'id'    => $siswa->id,
                'name'  => $siswa->name,
                'nis'   => $siswa->nis,
                'class' => $siswa->kelas?->nama_kelas ?? $siswa->class,
            ],
            'grades' => $rows->map(fn ($g) => [
                'id'          => $g->id,
                'subject'     => $g->subject,
                'daily_score' => $g->daily_score,
                'exam_score'  => $g->exam_score,
            ])->values(),
            'limited' => $allowed !== null,
        ]);
    }

    /**
     * Update biodata (admin/operator & walikelas kelas tsb).
     * Walikelas tidak boleh mengubah NIS, kelas, dan status.
     */
    public function update(Request $request, $id)
    {
        $siswa = Student::findOrFail($id);
        Gate::authorize('viewDetail', $siswa);

        $isAdmin = $request->user()->isAdminLike();

        $rules = [
            'name'         => 'required|string|max:255',
            'gender'       => ['required', Rule::in(['L', 'P'])],
            'birth_place'  => 'nullable|string|max:255',
            'birth_date'   => 'nullable|date',
            'parent_name'  => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:30',
            'dormitory'    => 'nullable|string|max:100',
            'address'      => 'nullable|string',
        ];

        if ($isAdmin) {
            $rules += [
                'nis'      => ['required', 'string', 'max:50', Rule::unique('students', 'nis')->ignore($siswa->id)],
                'kelas_id' => 'nullable|exists:kelas,id',
                'status'   => ['required', Rule::in(['Aktif', 'Lulus', 'Keluar'])],
            ];
        }

        $data = $request->validate($rules);

        if ($isAdmin) {
            $this->applyKelas($data);
        }

        $siswa->update($data);

        return back()->with('success', 'Biodata siswa berhasil diperbarui.');
    }

    /**
     * Simpan / perbarui nilai.
     * - Walikelas/admin: semua mapel.
     * - Guru mapel: hanya mapel yang ia ajarkan di kelas siswa tsb.
     */
    public function updateGrades(Request $request, $id)
    {
        $siswa = Student::findOrFail($id);
        Gate::authorize('grade', $siswa);

        $request->validate([
            'grades'               => 'required|array|min:1',
            'grades.*.id'          => 'nullable|integer',
            'grades.*.subject'     => 'required|string|max:255|distinct',
            'grades.*.daily_score' => 'nullable|integer|min:0|max:100',
            'grades.*.exam_score'  => 'nullable|integer|min:0|max:100',
        ]);

        $allowed = $request->user()->gradableSubjects($siswa); // null = semua
        $allowedMap = $allowed === null ? null : collect($allowed)->mapWithKeys(
            fn ($s) => [mb_strtolower($s) => $s]
        );

        foreach ($request->grades as $row) {
            $subject = trim($row['subject']);

            if ($allowedMap !== null) {
                // Guru mapel: nama mapel dipaksa sesuai miliknya; mapel lain ditolak
                $canonical = $allowedMap[mb_strtolower($subject)] ?? null;
                abort_if($canonical === null, 403, "Anda tidak berwenang menilai mapel \"{$subject}\".");
                $subject = $canonical;
            }

            $daily = $row['daily_score'] ?? null;
            $exam  = $row['exam_score'] ?? null;

            $scores = array_filter([$daily, $exam], fn ($v) => $v !== null && $v !== '');
            $final  = count($scores) ? round(array_sum($scores) / count($scores)) : null;

            $payload = [
                'daily_score' => $daily,
                'exam_score'  => $exam,
                'final_score' => $final,
            ];

            if ($allowedMap === null && ! empty($row['id'])) {
                Grade::where('id', $row['id'])
                    ->where('student_id', $siswa->id)
                    ->update($payload + ['subject' => $subject]);
            } else {
                Grade::updateOrCreate(
                    ['student_id' => $siswa->id, 'subject' => $subject],
                    $payload
                );
            }
        }

        return back()->with('success', 'Nilai berhasil disimpan.');
    }

    public function destroyGrade($id, $gradeId)
    {
        $siswa = Student::findOrFail($id);
        Gate::authorize('viewDetail', $siswa);

        Grade::where('id', $gradeId)->where('student_id', $siswa->id)->delete();

        return back()->with('success', 'Mata pelajaran dihapus.');
    }

    /**
     * Catat kehadiran (admin/operator & walikelas kelas tsb).
     */
    public function storeAttendance(Request $request, $id)
    {
        $siswa = Student::findOrFail($id);
        Gate::authorize('viewDetail', $siswa);

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
        $siswa = Student::findOrFail($id);
        Gate::authorize('viewDetail', $siswa);

        StudentAttendance::where('id', $attendanceId)->where('student_id', $siswa->id)->delete();

        return back()->with('success', 'Catatan kehadiran dihapus.');
    }

    // ------------------------------------------------------------------

    private function kelasOptions()
    {
        return Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get(['id', 'nama_kelas', 'tingkat']);
    }

    /** Isi kolom class & homeroom_teacher otomatis dari kelas yang dipilih */
    private function applyKelas(array &$data): void
    {
        if (! array_key_exists('kelas_id', $data)) {
            return;
        }

        $kelas = $data['kelas_id'] ? Kelas::with('walikelas:id,nama')->find($data['kelas_id']) : null;

        $data['class'] = $kelas?->nama_kelas;
        $data['homeroom_teacher'] = $kelas?->walikelas?->nama;
    }
}
