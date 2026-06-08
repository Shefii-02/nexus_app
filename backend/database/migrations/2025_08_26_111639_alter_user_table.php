<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Use timestamp with CURRENT_TIMESTAMP as default
            $table->timestamp('last_activation')
                  ->nullable()
                  ->default(DB::raw('CURRENT_TIMESTAMP'))
                  ->after('updated_at'); // optional positioning
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('last_activation');
        });
    }
};
