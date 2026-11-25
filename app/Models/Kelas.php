<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kelas extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kelas',
        'tingkat',
        'rombel',       
        'walikelas_id', 
    ];

    /**
     * Relasi ke Santri (Siswa) melalui tabel pivot kelas_santri
     */
    public function santris()
    {
        return $this->belongsToMany(Student::class, 'kelas_santri', 'kelas_id', 'santri_id')
                    ->withTimestamps();
    }

    /**
     * Relasi ke Guru Pengajar (Many-to-Many)
     * DIUBAH: Dari User::class menjadi Guru::class
     */
    public function gurus()
    {
        return $this->belongsToMany(Guru::class, 'kelas_guru', 'kelas_id', 'guru_id')
                    ->withPivot('mata_pelajaran', 'hari', 'jam_mulai', 'jam_selesai')
                    ->withTimestamps();
    }

    /**
     * Relasi ke Walikelas (1 Kelas punya 1 Wali Kelas)
     * DIUBAH: Dari User::class menjadi Guru::class
     */
    public function walikelas()
    {
        return $this->belongsTo(Guru::class, 'walikelas_id');
    }

    /**
     * Relasi ke Absensi
     */
    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }
}