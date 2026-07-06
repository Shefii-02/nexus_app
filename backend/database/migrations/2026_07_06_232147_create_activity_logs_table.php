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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
$table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('module', 100);          // Student, Teacher, Payment...
            $table->string('action', 100);          // Created, Updated, Deleted...

            $table->string('description')->nullable();

            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();

            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();

            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();

            $table->string('device')->nullable();
            $table->string('platform')->nullable();
            $table->string('browser')->nullable();

            $table->timestamps();

            $table->index(['module']);
            $table->index(['action']);
            $table->index(['user_id']);
            $table->index(['model_type', 'model_id']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
