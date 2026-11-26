<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // PAKSA IZINKAN SEMUA ORANG LIHAT DAFTAR
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function viewDetail(User $user, Student $student): bool
    {
        return true; // PAKSA IZINKAN SEMUA ORANG LIHAT DETAIL
    }

    public function grade(User $user, Student $student): bool
    {
        return true; // PAKSA IZINKAN SEMUA ORANG INPUT NILAI
    }
}