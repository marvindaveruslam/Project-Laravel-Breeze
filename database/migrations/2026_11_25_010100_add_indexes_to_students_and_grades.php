<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->index('name');
            $table->index('class');
        });

        Schema::table('grades', function (Blueprint $table) {
            $table->unique(['student_id', 'subject']);
        });
    }

    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->dropUnique(['student_id', 'subject']);
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['class']);
        });
    }
};
