<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_url')->nullable();
            $table->string('material_type')->default('document'); // pdf, video, document, link, etc.
            $table->integer('order')->default(0);
            $table->enum('status', ['active', 'inactive', 'archived'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['course_id', 'status']);
            $table->index('material_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_materials');
    }
};
