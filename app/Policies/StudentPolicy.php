<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    /** Boleh membuka daftar siswa (hasilnya sendiri sudah dibatasi per kelas) */
    public function viewAny(User $user): bool
    {
        return $user->isAdminLike() || $user->isTeacher();
    }

    /** Hanya admin/operator yang boleh menambah siswa */
    public function create(User $user): bool
    {
        return $user->isAdminLike();
    }

    /**
     * Halaman detail lengkap (biodata, semua nilai, kehadiran, dst).
     * Hanya admin/operator dan WALIKELAS dari kelas siswa tersebut.
     */
    public function viewDetail(User $user, Student $student): bool
    {
        if ($user->isAdminLike()) {
            return true;
        }

        return $student->kelas_id !== null
            && in_array((int) $student->kelas_id, $user->walikelasKelasIds(), true);
    }

    /**
     * Boleh menginput nilai: yang bisa lihat detail, atau guru yang
     * mengajar di kelas siswa tersebut (hanya mapelnya sendiri).
     */
    public function grade(User $user, Student $student): bool
    {
        if ($this->viewDetail($user, $student)) {
            return true;
        }

        return $student->kelas_id !== null
            && in_array((int) $student->kelas_id, $user->teachingKelasIds(), true)
            && count($user->gradableSubjects($student) ?? []) > 0;
    }
}
