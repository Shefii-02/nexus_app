<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
                $table->decimal('amount', 10, 2);
                $table->date('payment_date');
                $table->enum('payment_method', ['cash', 'check', 'bank_transfer', 'other'])->default('cash');
                $table->string('reference_number')->nullable();
                $table->text('notes')->nullable();
                $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
                $table->timestamps();

                $table->index(['student_id', 'course_id', 'status']);
                $table->index('payment_date');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
