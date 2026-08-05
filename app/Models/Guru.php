<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'nip',
        'jenis_kelamin',
        'no_hp',
        'alamat',
        'mata_pelajaran',
    ];

    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }
    public function mataPelajarans()
    {
        return $this->hasMany(MataPelajaran::class);
    }
}
