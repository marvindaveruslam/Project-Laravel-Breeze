<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelasguru extends Model
{
    protected $table = 'kelas_guru';

    protected $fillable = [
        'kelas_id',
        'guru_id',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }
}
