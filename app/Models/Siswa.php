<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nis',
        'nama_lengkap',
        'jenis_kelamin',
        'kelas',
        // tambahkan kolom lain jika ada (misal: no_hp, alamat)
    ];

    // Relasi: 1 Siswa punya banyak Catatan
    public function catatan()
    {
        return $this->hasMany(CatatanSiswa::class);
    }
}