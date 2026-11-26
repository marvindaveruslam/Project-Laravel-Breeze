<?php

namespace App\Imports;

use App\Models\Kelas;
use App\Models\Guru;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GuruKelasImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // 1. Cari atau buat kelas berdasarkan nama_kelas
        $kelas = Kelas::firstOrCreate(
            ['nama_kelas' => $row['nama_kelas']],
            [
                'tingkat' => 'Belum Diatur'
            ]
        );

        // Ambil nama mapel dari Excel (antisipasi typo 'mata_pelajar' atau 'mata_pelajaran')
        $mapel = $row['mata_pelajaran'] ?? $row['mata_pelajar'] ?? 'Umum';

        // 2. Cari atau buat guru berdasarkan nama_guru
        $guru = Guru::firstOrCreate(
            ['nama' => $row['nama_guru']],
            [
                // NIP DIBUAT UNIK OTOMATIS AGAR TIDAK ERROR "Duplicate entry"
                'nip'            => 'NIP-' . uniqid(), 
                'jenis_kelamin'  => 1, // 1 = Laki-laki (sesuaikan dengan database)
                'mata_pelajaran' => $mapel
            ]
        );

        // 3. Jika guru ditandai sebagai walikelas (is_walikelas == 1)
        if (isset($row['is_walikelas']) && $row['is_walikelas'] == 1) {
            $kelas->update(['walikelas_id' => $guru->id]);
        }

        // 4. Masukkan ke tabel pivot kelas_guru (Jadwal)
        $existingPivot = $kelas->gurus()
            ->where('guru_id', $guru->id)
            ->wherePivot('mata_pelajaran', $mapel)
            ->wherePivot('hari', $row['hari'])
            ->first();

        if (!$existingPivot) {
            $kelas->gurus()->attach($guru->id, [
                'mata_pelajaran' => $mapel,
                'hari'           => $row['hari'] ?? '-',
                'jam_mulai'      => $row['jam_mulai'] ?? '00:00',
                'jam_selesai'    => $row['jam_selesai'] ?? '00:00',
            ]);
        }

        return null;
    }
}