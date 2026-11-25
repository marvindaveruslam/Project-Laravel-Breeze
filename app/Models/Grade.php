<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    // Kolom yang bisa diisi
    protected $fillable = [
        'student_id', 
        'subject', 
        'daily_score', 
        'exam_score', 
        'final_score'
    ];

    /**
     * Relasi kebalikan: Nilai ini milik seorang Siswa.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}