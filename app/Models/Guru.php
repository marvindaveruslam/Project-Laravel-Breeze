<?php
// app/Models/Guru.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;

    protected $table = 'gurus'; // Pastikan nama tabelnya sesuai

    protected $fillable = [
        'nama',
        'nip',
        'jenis_kelamin',
        'no_hp',
        'alamat',
        'mata_pelajaran',
    ];

    /**
     * Relasi ke Absensi
     */
    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }

    /**
     * Relasi ke Mata Pelajaran
     */
    public function mataPelajarans()
    {
        return $this->hasMany(MataPelajaran::class);
    }

    /**
     * TAMBAHAN: Relasi ke Kelas (Many-to-Many)
     * Digunakan untuk mengetahui guru ini mengajar di kelas mana saja
     */
    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'kelas_guru', 'guru_id', 'kelas_id')
                    ->withPivot('mata_pelajaran', 'hari', 'jam_mulai', 'jam_selesai')
                    ->withTimestamps();
    }

    /**
     * TAMBAHAN: Relasi sebagai Wali Kelas
     * Digunakan untuk mengetahui guru ini menjadi wali kelas di kelas mana
     */
    public function waliKelas()
    {
        return $this->hasOne(Kelas::class, 'walikelas_id');
    }
}