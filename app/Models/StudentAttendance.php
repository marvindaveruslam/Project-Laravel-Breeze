<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
    public const STATUSES = ['hadir', 'izin', 'sakit', 'alpa'];

    protected $fillable = ['student_id', 'date', 'status', 'note'];

    protected $casts = [
        'date' => 'date:Y-m-d',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
