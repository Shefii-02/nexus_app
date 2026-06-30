<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('conversation_id')->nullable()
                ->constrained()->nullOnDelete();

            // ringing   -> just placed, waiting for teacher to answer
            // active    -> teacher answered, call in progress
            // missed    -> rang out (30s timeout, nobody answered)
            // rejected  -> teacher declined
            // ended     -> normally ended by either party
            // failed    -> could not be placed (e.g. teacher busy)
            $table->enum('status', [
                'ringing', 'active', 'missed', 'rejected', 'ended', 'failed',
            ])->default('ringing');

            $table->enum('type', ['audio', 'video'])->default('audio');

            $table->timestamp('started_at')->nullable();   // when placed
            $table->timestamp('answered_at')->nullable();  // when teacher accepted
            $table->timestamp('ended_at')->nullable();     // when it finished

            $table->timestamps();

            $table->index(['teacher_id', 'status']);
            $table->index(['course_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calls');
    }
};
