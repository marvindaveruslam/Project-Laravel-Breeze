<?php

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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            
            // --- TAMBAHKAN KOLOM INI ---
            $table->string('nis')->unique(); // Nomor Induk Siswa
            $table->string('name'); // Nama Lengkap
            $table->enum('gender', ['L', 'P']); // Laki-laki / Perempuan
            $table->string('class')->nullable(); // Kelas (misal: "9")
            
            // Biodata Lengkap
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('parent_name')->nullable(); // Nama Orang Tua/Wali
            $table->string('parent_phone')->nullable(); // No HP Wali
            $table->string('dormitory')->nullable(); // Kamar Asrama (jika ada)
            $table->text('address')->nullable(); // Alamat Asal
            
            // Data Tambahan
            $table->string('homeroom_teacher')->nullable(); // Nama Walikelas
            $table->enum('status', ['Aktif', 'Lulus', 'Keluar'])->default('Aktif');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};