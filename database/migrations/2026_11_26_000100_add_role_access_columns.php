<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Akun login milik guru (menghubungkan tabel users <-> gurus)
        Schema::table('gurus', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->unique()->constrained('users')->nullOnDelete();
        });

        // Siswa terhubung ke kelas, supaya walikelas/guru bisa dibatasi per kelas
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('kelas_id')->nullable()->after('class')->constrained('kelas')->nullOnDelete();
            $table->index('kelas_id');
        });

        // Isi otomatis kelas_id dari kolom "class" lama (cocok dengan nama_kelas)
        $kelas = DB::table('kelas')->get(['id', 'nama_kelas', 'walikelas_id']);
        foreach ($kelas as $k) {
            DB::table('students')
                ->whereNull('kelas_id')
                ->whereRaw('LOWER(TRIM(class)) = ?', [mb_strtolower(trim($k->nama_kelas))])
                ->update(['kelas_id' => $k->id]);
        }
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['kelas_id']);
            $table->dropIndex(['kelas_id']);
            $table->dropColumn('kelas_id');
        });

        Schema::table('gurus', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
