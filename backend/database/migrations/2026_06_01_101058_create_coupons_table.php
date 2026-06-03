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
        Schema::create('coupons', function (Blueprint $table) {

            $table->id();

            $table->string('code')->unique();

            $table->string('title');

            $table->text('description')->nullable();

            $table->enum('discount_type', [
                'fixed',
                'percentage'
            ]);

            $table->decimal('discount_value', 12, 2);

            $table->decimal('max_discount_amount', 12, 2)
                ->nullable();

            $table->decimal('minimum_amount', 12, 2)
                ->default(0);

            $table->integer('usage_limit')
                ->nullable();

            $table->integer('usage_per_user')
                ->default(1);

            $table->date('start_date');

            $table->date('end_date');

            $table->enum('apply_on', [
                'admission',
                'renewal',
                'both'
            ])->default('both');

            $table->tinyInteger('is_active')
                ->default(1);

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users');

            $table->timestamps();

            $table->softDeletes();
        });

        Schema::create('coupon_usages', function (Blueprint $table) {

            $table->id();

            $table->foreignId('coupon_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users');

            $table->foreignId('admission_id')
                ->nullable()
                ->constrained('admissions')
                ->nullOnDelete();

            $table->foreignId('renewal_id')
                ->nullable()
                ->constrained('admission_renewals')
                ->nullOnDelete();

            $table->decimal('original_amount', 12, 2);

            $table->decimal('discount_amount', 12, 2);

            $table->decimal('final_amount', 12, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('coupon_usages');

    }
};
