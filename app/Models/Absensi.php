<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    public const HADIR = 1;
    public const IZIN = 2;
    public const SAKIT = 3;
    public const TANPA_KETERANGAN = 4;

    protected $fillable = [
        'santri_id',
        'kelas_siswa_id',
        'guru_id',
        'tanggal',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }

    public function kelasSiswa()
    {
        return $this->belongsTo(KelasSiswa::class);
    }

    public function kelasGuru()
    {
        return $this->belongsTo(KelasGuru::class);
    }

}
