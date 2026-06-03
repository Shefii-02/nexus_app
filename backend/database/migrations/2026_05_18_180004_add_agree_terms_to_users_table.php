<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Check if column already exists before adding
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'agree_terms')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('agree_terms')->default(false)->after('email_verified_at');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'agree_terms')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('agree_terms');
            });
        }
    }
};
