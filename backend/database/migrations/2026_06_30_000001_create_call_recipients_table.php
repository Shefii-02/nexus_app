<?php
// database/migrations/2026_06_30_000001_create_call_recipients_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('call_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('call_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();

            // ringing  -> pushed, waiting for this student to act
            // answered -> this student joined
            // missed   -> rang out (30s) with no response
            // rejected -> student declined
            // ended    -> was answered, then ended
            // cancelled-> teacher ended the whole broadcast before student acted
            $table->enum('status', [
                'ringing', 'answered', 'missed', 'rejected', 'ended', 'cancelled',
            ])->default('ringing');

            $table->timestamp('answered_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();

            $table->unique(['call_id', 'student_id']);
            $table->index(['student_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('call_recipients');
    }
};
