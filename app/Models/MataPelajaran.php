<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    protected $table = 'mata_pelajarans';

    protected $fillable = [
        'kode_mapel',
        'nama_mapel',
        'deskripsi',
    ];

    public function gurus()
    {
        return $this->hasMany(Guru::class, 'mata_pelajaran', 'kode_mapel');
    }
}

