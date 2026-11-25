<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kelas_guru', function (Blueprint $table) {
            // Menambahkan kolom jadwal
            $table->string('mata_pelajaran')->nullable()->after('guru_id');
            $table->string('hari')->nullable()->after('mata_pelajaran');
            $table->time('jam_mulai')->nullable()->after('hari');
            $table->time('jam_selesai')->nullable()->after('jam_mulai');
        });
    }

    public function down(): void
    {
        Schema::table('kelas_guru', function (Blueprint $table) {
            $table->dropColumn(['mata_pelajaran', 'hari', 'jam_mulai', 'jam_selesai']);
        });
    }
};