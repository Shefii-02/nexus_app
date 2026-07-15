<?php

// database/migrations/xxxx_xx_xx_create_polls_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('polls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->unique()->constrained('messages')->cascadeOnDelete();
            $table->foreignId('conversation_id')->constrained('conversations')->cascadeOnDelete(); // denormalized, avoids a join on every poll query
            $table->foreignId('created_by')->constrained('users');
            $table->string('question', 500);
            $table->boolean('allow_multiple_votes')->default(false);
            $table->boolean('is_closed')->default(false);
            $table->timestamp('closes_at')->nullable();
            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('polls');
    }
};
