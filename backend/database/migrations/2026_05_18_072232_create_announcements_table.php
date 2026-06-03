<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('announcements')) {
            Schema::create('announcements', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('content');
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->enum('target_type', ['all_users', 'all_staffs','all_students', 'all_teachers', 'selected_users', 'roles', 'batches', 'specific'])->default('all_users');
                $table->dateTime('start_date')->nullable();
                $table->dateTime('end_date')->nullable();
                $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
                $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
                $table->timestamps();
                $table->softDeletes();

                $table->index(['created_by', 'status', 'start_date']);
            });
        }

        if (!Schema::hasTable('announcement_user')) {
            Schema::create('announcement_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('announcement_id')->constrained('announcements')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->timestamps();

                $table->unique(['announcement_id', 'user_id']);
            });
        }

        if (!Schema::hasTable('announcement_batch')) {
            Schema::create('announcement_batch', function (Blueprint $table) {
                $table->id();
                $table->foreignId('announcement_id')->constrained('announcements')->onDelete('cascade');
                $table->foreignId('batch_id')->constrained('batches')->onDelete('cascade');
                $table->timestamps();

                $table->unique(['announcement_id', 'batch_id']);
            });
        }

        if (!Schema::hasTable('announcement_role')) {
            Schema::create('announcement_role', function (Blueprint $table) {
                $table->id();
                $table->foreignId('announcement_id')->constrained('announcements')->onDelete('cascade');
                $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
                $table->timestamps();

                $table->unique(['announcement_id', 'role_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('announcement_role');
        Schema::dropIfExists('announcement_batch');
        Schema::dropIfExists('announcement_user');
        Schema::dropIfExists('announcements');
    }
};
