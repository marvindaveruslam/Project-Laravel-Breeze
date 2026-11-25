<?php

namespace App\Console\Commands;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class BuatAkunGuru extends Command
{
    protected $signature = 'guru:akun {guru_id : ID di tabel gurus} {email} {password}';

    protected $description = 'Buat akun login untuk seorang guru dan hubungkan ke data gurunya (role otomatis: walikelas jika ia walikelas, selain itu guru)';

    public function handle(): int
    {
        $guru = Guru::find($this->argument('guru_id'));
        if (! $guru) {
            $this->error('Guru tidak ditemukan.');
            return self::FAILURE;
        }

        $role = Kelas::where('walikelas_id', $guru->id)->exists() ? 'walikelas' : 'guru';

        $user = User::updateOrCreate(
            ['email' => $this->argument('email')],
            ['name' => $guru->nama, 'role' => $role, 'password' => Hash::make($this->argument('password'))]
        );

        $guru->update(['user_id' => $user->id]);

        $this->info("Akun {$user->email} dibuat/diperbarui dengan role '{$role}' dan ditautkan ke {$guru->nama}.");
        $this->line($role === 'walikelas' ? 'Login di: /login/walikelas' : 'Login di: /login/guru');

        return self::SUCCESS;
    }
}
