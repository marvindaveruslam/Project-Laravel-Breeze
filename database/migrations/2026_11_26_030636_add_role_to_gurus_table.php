<?php
// database/migrations/2026_11_25_100000_add_role_to_gurus_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('gurus', function (Blueprint $table) {
            // Menambahkan kolom 'role' setelah kolom 'nama'
            // Default: 'guru_mapel'
            $table->string('role')->default('guru_mapel')->after('nama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gurus', function (Blueprint $table) {
            // Menghapus kolom 'role' jika migration di-rollback
            $table->dropColumn('role');
        });
    }
};