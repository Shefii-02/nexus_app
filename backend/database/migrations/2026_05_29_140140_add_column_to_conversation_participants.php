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
        Schema::table('conversation_participants', function (Blueprint $table) {
            //
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->boolean('is_pinned')->default(false);
            $table->string('status')->default('active')->comment('active,suspended,left');
            $table->dateTime('left_at')->nullable();
            $table->softDeletes();
        });
        Schema::table('conversations', function (Blueprint $table) {
            $table->string('avatar')->nullable();
            $table->integer('module_id')->nullable();
            $table->string('status')->default('active')->comment('active,suspended,deleted');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            //
        });
    }
};
