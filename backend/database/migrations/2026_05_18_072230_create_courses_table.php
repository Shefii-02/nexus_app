<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('courses')) {
            Schema::create('courses', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('name');
                $table->text('description')->nullable();
                $table->foreignId('teacher_id')->nullable()->constrained('users')->onDelete('set null');
                $table->enum('fee_type', ['monthly', 'one_time'])->default('one_time');
                $table->integer('duration_days')->default(1);
                $table->enum('status', ['active', 'inactive', 'archived'])->default('active');
                $table->timestamps();
                $table->softDeletes();

                $table->index(['teacher_id', 'status']);
                $table->index('code');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
