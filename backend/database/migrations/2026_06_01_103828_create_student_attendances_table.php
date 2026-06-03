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
        Schema::create('student_attendances', function (Blueprint $table) {

            $table->id();

            $table->foreignId('student_id')
                ->constrained('users');

            $table->foreignId('course_id')
                ->constrained('courses');

            $table->foreignId('batch_id')
                ->nullable()
                ->constrained('batches');

            $table->date('attendance_date');

            $table->enum('status', [
                'present',
                'absent',
                'late',
                'leave'
            ]);

            $table->text('remarks')
                ->nullable();

            $table->foreignId('marked_by')
                ->nullable()
                ->constrained('users');

            $table->timestamps();

            $table->unique([
                'student_id',
                'course_id',
                'attendance_date'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_attendances');
    }
};
