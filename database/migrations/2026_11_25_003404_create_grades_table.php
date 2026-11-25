<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            // Menghubungkan ke tabel students. Jika tabel siswa Anda bernama 'santris', ganti 'students' jadi 'santris'
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            
            $table->string('subject'); // Mata Pelajaran (Al-Quran Hadits, Fiqih, dll)
            $table->integer('daily_score')->nullable(); // Nilai Harian
            $table->integer('exam_score')->nullable(); // Nilai Ulangan
            $table->integer('final_score')->nullable(); // Rata-rata
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};