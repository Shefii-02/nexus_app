<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_batch', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('batch_id')->constrained('batches')->onDelete('cascade');
            $table->dateTime('admitted_at')->nullable();
            $table->dateTime('graduated_at')->nullable();
            $table->enum('status', ['active', 'graduated', 'inactive', 'suspended'])->default('active');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['student_id', 'batch_id']);
            $table->index(['batch_id', 'status']);
            $table->index('admitted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_batch');
    }
};
