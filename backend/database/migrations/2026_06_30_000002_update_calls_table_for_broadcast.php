<?php
// database/migrations/2026_06_30_000002_update_calls_table_for_broadcast.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('calls', function (Blueprint $table) {
            $table->foreignId('student_id')->nullable()->change();
            $table->enum('caller_role', ['student', 'teacher'])->default('student')->after('teacher_id');
            $table->foreignId('caller_id')->nullable()->constrained('users')->nullOnDelete()->after('caller_role');
        });
    }

    public function down(): void
    {
        Schema::table('calls', function (Blueprint $table) {
            $table->dropConstrainedForeignId('caller_id');
            $table->dropColumn('caller_role');
            $table->foreignId('student_id')->nullable(false)->change();
        });
    }
};
