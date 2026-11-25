<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    /**
     * Kolom-kolom yang diizinkan untuk diisi secara massal (Mass Assignment).
     * Ini harus sesuai dengan kolom yang ada di file migration students.
     */
    protected $fillable = [
        'nis', 
        'name', 
        'gender', 
        'class', 
        'kelas_id',
        'birth_place', 
        'birth_date', 
        'parent_name', 
        'parent_phone', 
        'dormitory', 
        'address', 
        'homeroom_teacher', 
        'status'
    ];

    /**
     * Relasi: Satu Siswa memiliki banyak Nilai (Grades).
     */
    public function grades()
    {
        // Menghubungkan ke model Grade
        return $this->hasMany(Grade::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    /**
     * Relasi: Satu Siswa memiliki banyak catatan kehadiran.
     */
    public function attendances()
    {
        return $this->hasMany(StudentAttendance::class);
    }

    protected $casts = [
        'birth_date' => 'date:Y-m-d',
    ];
}