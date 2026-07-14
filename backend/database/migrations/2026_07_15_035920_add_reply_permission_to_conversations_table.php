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
        Schema::table('conversations', function (Blueprint $table) {
            //
            Schema::table('conversations', function (Blueprint $table) {
                // admin        → only admin can send messages
                // staff        → admin + staff can send messages
                // teacher      → admin + staff + teacher can send messages
                // all          → everyone (incl. students) can send messages
                $table->enum('reply_permission', ['admin', 'staff', 'teacher', 'all'])
                    ->default('all')
                    ->after('status');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            //
            $table->dropColumn('reply_permission');
        });
    }
};
