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

            Schema::create('teachers_courses', function (Blueprint $table) {
                $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
                $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
                $table->timestamps();
            });


        // Schema::table('course_classes', function (Blueprint $table) {
        //     $table->string('class_number')->nullable()->change();
        //     $table->timestamp('started_at')->nullable()->after('class_number');
        //     $table->timestamp('ended_at')->nullable()->after('started_at');
        //     $table->enum('status', ['draft', 'scheduled', 'completed', 'cancelled'])->change();
        //     $table->softDeletes();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers_courses');
    }
};
