<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $fillable = [
        'nama_kelas',
        'tingkat',
    ];

    public function santris()
    {
        return $this->hasMany(KelasSantri::class);
    }

    public function gurus()
    {
        return $this->hasMany(KelasGuru::class);
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }

}
