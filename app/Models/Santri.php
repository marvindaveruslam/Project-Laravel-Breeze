<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Santri extends Model
{
        protected $fillable = [
        'nis',
        'nama',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'no_hp',
        'kelas_id',
    ];

    public function kelas()
    {
    return $this->belongsTo(Kelas::class);
    }

    public function kelasSantris()
    {
        return $this->hasMany(KelasSantri::class);
    }

    public function absensis()
    {
    return $this->hasMany(Absensi::class);
    }
}
