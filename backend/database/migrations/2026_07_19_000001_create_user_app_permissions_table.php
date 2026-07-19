<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_app_permissions', function (Blueprint $table) {
            $table->id();

            // The user this permission belongs to (staff, teacher, admin, etc.)
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // One of: group_manage, individual_chat_manage, course_manage,
            // admission_manage, teacher_manage, staff_manage, student_manage
            $table->string('permission_key');

            $table->boolean('granted')->default(false);

            $table->timestamps();

            // One row per (user, permission_key) — upsert on that pair when saving.
            $table->unique(['user_id', 'permission_key']);
            $table->index('permission_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_app_permissions');
    }
};
