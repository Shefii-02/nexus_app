<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {

            // 🖼 Thumbnail
            $table->string('thumbnail')->nullable()->after('description');

            // 📅 Dates
            $table->date('started_at')->nullable()->after('description');
            $table->date('ended_at')->nullable()->after('started_at');

            // 💰 Pricing
            $table->decimal('actual_price', 10, 2)->default(0)->after('ended_at');
            $table->decimal('net_price', 10, 2)->default(0)->after('actual_price');

            // 🎟 Coupon
            $table->boolean('coupon_available')->default(false)->after('net_price');

            // 🔄 Renewal
            $table->boolean('is_renewal')->default(false)->after('coupon_available');

            // 🧑‍🏫 Class Type
            $table->enum('class_type', ['online', 'offline', 'hybrid'])
                  ->default('online')
                  ->after('is_renewal');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {

            $table->dropColumn([
                'thumbnail',
                'started_at',
                'ended_at',
                'actual_price',
                'net_price',
                'coupon_available',
                'is_renewal',
                'class_type',
            ]);
        });
    }
};
