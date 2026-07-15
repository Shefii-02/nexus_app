<?php
// database/migrations/xxxx_xx_xx_add_poll_type_to_messages_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // MySQL enums need a raw ALTER — adjust if you're on Postgres (use a check constraint instead)
        DB::statement("ALTER TABLE messages MODIFY COLUMN type
            ENUM('text','image','video','audio','file','voice','poll') NOT NULL DEFAULT 'text'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE messages MODIFY COLUMN type
            ENUM('text','image','video','audio','file','voice') NOT NULL DEFAULT 'text'");
    }
};
