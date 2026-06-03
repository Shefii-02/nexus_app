<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_renewals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->dateTime('renewal_date');
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'verified', 'rejected', 'paid'])->default('pending');
            $table->string('payment_reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'course_id', 'status']);
            $table->index(['renewal_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_renewals');
    }
};
