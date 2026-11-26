<?php
// database/seeders/GuruSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Guru;
use App\Models\Kelas;

class GuruSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================
        // 1. BUAT GURU MAPEL (GURU BIASA)
        // ==========================================
        // jenis_kelamin: 1 = Laki-laki, 0 = Perempuan
        $guruMapel = [
            ['nama' => 'Ustadz Ahmad Fauzi', 'nip' => '198001012001', 'jenis_kelamin' => 1, 'role' => 'guru_mapel', 'mata_pelajaran' => 'Fiqih'],
            ['nama' => 'Ustadzah Siti Aminah', 'nip' => '198502022002', 'jenis_kelamin' => 0, 'role' => 'guru_mapel', 'mata_pelajaran' => 'Bahasa Arab'],
            ['nama' => 'Ustadz Budi Santoso', 'nip' => '199003032003', 'jenis_kelamin' => 1, 'role' => 'guru_mapel', 'mata_pelajaran' => 'Matematika'],
            ['nama' => 'Ustadzah Nur Hidayah', 'nip' => '199204042004', 'jenis_kelamin' => 0, 'role' => 'guru_mapel', 'mata_pelajaran' => 'Bahasa Indonesia'],
        ];

        foreach ($guruMapel as $guru) {
            Guru::updateOrCreate(
                ['nip' => $guru['nip']],
                [
                    'nama' => $guru['nama'],
                    'jenis_kelamin' => $guru['jenis_kelamin'],
                    'role' => $guru['role'],
                    'mata_pelajaran' => $guru['mata_pelajaran'],
                ]
            );
        }

        // ==========================================
        // 2. BUAT WALIKELAS UNTUK 18 KELAS
        // ==========================================
        $daftarKelas = [
            '1A Tsanawiyah', '1B Tsanawiyah', '1C Tsanawiyah',
            '2A Tsanawiyah', '2B Tsanawiyah', '2C Tsanawiyah',
            '3A Tsanawiyah', '3B Tsanawiyah', '3C Tsanawiyah',
            '1A Aliyah', '1B Aliyah', '1C Aliyah',
            '2A Aliyah', '2B Aliyah', '2C Aliyah',
            '3A Aliyah', '3B Aliyah', '3C Aliyah',
        ];

        foreach ($daftarKelas as $index => $namaKelas) {
            $namaWalikelas = 'Ustadz Walikelas ' . $namaKelas;
            $nipWalikelas = '2001' . str_pad($index + 1, 4, '0', STR_PAD_LEFT);

            $walikelas = Guru::updateOrCreate(
                ['nip' => $nipWalikelas],
                [
                    'nama' => $namaWalikelas,
                    // Selang-seling: index genap = L (1), ganjil = P (0)
                    'jenis_kelamin' => ($index % 2 == 0) ? 1 : 0,
                    'role' => 'walikelas',
                    'mata_pelajaran' => 'Wali Kelas',
                ]
            );

            $kelas = Kelas::where('nama_kelas', $namaKelas)->first();
            if ($kelas) {
                $kelas->update(['walikelas_id' => $walikelas->id]);
            }
        }
    }
}