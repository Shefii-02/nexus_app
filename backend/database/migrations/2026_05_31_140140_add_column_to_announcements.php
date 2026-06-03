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
        Schema::table('announcements', function (Blueprint $table) {
            //
            $table->boolean('is_pinned')->default(false)->after('status');
            $table->integer('position')->default(1)->nullable()->after('is_pinned');
            $table->integer('image')->nullable()->after('id');
        });

        Schema::table('announcement_user', function (Blueprint $table) {
            //
            $table->string('fcm_status')->default('pending')->nullable()->after('user_id');
            $table->longText('fcm_response')->nullable()->after('fcm_status');
            $table->string('status')->default('pending')->nullable()->after('fcm_response');
            $table->timestamp('delivered_at')->nullable()->after('status');
            $table->timestamp('read_at')->nullable()->after('delivered_at');
            $table->timestamp('clicked_at')->nullable()->after('read_at');
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
