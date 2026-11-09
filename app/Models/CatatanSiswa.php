<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatatanSiswa extends Model
{
    use HasFactory;

    // 1. Daftarkan kolom yang boleh diisi (sesuai form & migration)
    protected $fillable = [
        'siswa_id',
        'jenis',
        'tanggal',
        'keterangan',
    ];

    // 2. Buat relasi ke model Siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}