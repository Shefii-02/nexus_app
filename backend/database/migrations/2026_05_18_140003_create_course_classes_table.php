<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('class_number');
            $table->dateTime('scheduled_date');
            $table->integer('duration_minutes')->default(60);
            $table->string('room_location')->nullable();
            $table->string('class_link')->nullable(); // Meet link, Zoom link, etc.
            $table->string('record_link')->nullable(); // Recording link
            $table->string('source')->default('google_meet')->nullable(); // zoom, google_meet, teams
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled');
            $table->timestamps();

            $table->index(['course_id', 'teacher_id', 'status']);
            $table->index('scheduled_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_classes');
    }
};
