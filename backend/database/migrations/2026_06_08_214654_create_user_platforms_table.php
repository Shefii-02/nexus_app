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
        Schema::create('user_platforms', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->default(0)->nullable(true);
            $table->string('platform')->default('android')->nullable(true);
            $table->string('ip_address')->nullable();
            $table->string('device_info')->nullable();
            $table->string('app_version')->nullable();
            $table->timestamp('last_active_at')->nullable();
            $table->string('fcm_token')->nullable();
            $table->string('voip_token')->nullable();
            $table->string('status')->nullable(true);
            $table->string('device_id')->nullable(true);
            $table->string('city')->nullable(true);
            $table->string('district')->nullable(true);
            $table->string('latitude')->nullable(true);
            $table->string('longitude')->nullable(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_platforms');
    }
};
