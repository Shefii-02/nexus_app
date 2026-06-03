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
        Schema::create('teacher_attendances', function (Blueprint $table) {

            $table->id();

            $table->foreignId('teacher_id')
                ->constrained('users');

            $table->foreignId('course_id')
                ->nullable()
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
                'teacher_id',
                'attendance_date',
                'course_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_attendances');
    }
};
