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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();

            $table->text('message')->nullable();

            $table->enum('type', [
                'text',
                'image',
                'file',
                'audio',
                'video',
                'class_link',
                'record_link'
            ])->default('text');

            $table->string('media_url')->nullable();

            $table->foreignId('reply_to')->nullable()->constrained('messages')->nullOnDelete();

            $table->timestamps();

            // ⚡ indexes for performance
            $table->index(['conversation_id', 'created_at']);
            $table->index(['sender_id']);
        });
        Schema::create('message_reactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('message_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('reaction'); // 👍 ❤️ 😂

            $table->timestamps();

            $table->unique(['message_id', 'user_id']); // one reaction per user
        });

        Schema::create('message_reads', function (Blueprint $table) {
            $table->id();

            $table->foreignId('message_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->timestamp('read_at')->nullable();

            $table->timestamps();

            $table->unique(['message_id', 'user_id']); // one read record per user
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('message_reactions');
        Schema::dropIfExists('message_reads');
    }
};
