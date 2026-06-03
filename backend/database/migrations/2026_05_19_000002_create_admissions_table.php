<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('admissions')) {


            Schema::create('admissions', function (Blueprint $table) {

                $table->id();

                $table->foreignId('student_id')
                    ->constrained('users');

                $table->foreignId('course_id')
                    ->constrained('courses');

                $table->decimal('actual_fee', 12, 2);

                $table->decimal('discount_amount', 12, 2)
                    ->default(0);

                $table->text('discount_reason')
                    ->nullable();

                $table->foreignId('coupon_id')
                    ->nullable()
                    ->constrained('coupons');

                $table->decimal('net_fee', 12, 2);

                $table->date('admission_date');

                $table->date('expiry_date')
                    ->nullable();

                $table->enum('status', [
                    'active',
                    'expired',
                    'completed',
                    'cancelled'
                ])->default('active');

                $table->text('notes')->nullable();

                $table->foreignId('created_by')
                    ->constrained('users');

                $table->timestamps();
                $table->softDeletes();
            });


        }


        Schema::create('admission_renewals', function (Blueprint $table) {

            $table->id();

            $table->foreignId('admission_id')
                ->constrained('admissions')
                ->cascadeOnDelete();

            $table->foreignId('student_id')
                ->constrained('users');

            $table->foreignId('course_id')
                ->constrained('courses');

            $table->date('current_expiry_date');

            $table->date('renewal_from');

            $table->date('renewal_to');

            $table->decimal(
                'amount',
                12,
                2
            );

            $table->decimal(
                'discount_amount',
                12,
                2
            )->default(0);

            $table->decimal(
                'final_amount',
                12,
                2
            );

            $table->dateTime('paid_at')
                ->nullable();

            $table->enum(
                'status',
                [
                    'pending',
                    'paid',
                    'cancelled'
                ]
            )->default('pending');

            $table->text('remarks')
                ->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users');

            $table->timestamps();

            $table->softDeletes();

            $table->index([
                'status',
                'renewal_to'
            ]);
        });

        Schema::create('admission_payments', function (Blueprint $table) {

            $table->id();

            $table->foreignId('admission_id')
                ->constrained('admissions')
                ->cascadeOnDelete();

            $table->foreignId('student_id')
                ->constrained('users');

            $table->foreignId('course_id')
                ->constrained('courses');

            $table->decimal('amount', 12, 2);

            $table->enum('payment_method', [
                'cash',
                'upi',
                'card',
                'bank_transfer'
            ]);

            $table->string('transaction_no')
                ->nullable();

            $table->text('remarks')
                ->nullable();

            $table->dateTime('paid_at');

            $table->foreignId('received_by')
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

    public function down(): void
    {

        Schema::dropIfExists('coupon_usages');
        Schema::dropIfExists('admissions');
        Schema::dropIfExists('admission_renewals');
        Schema::dropIfExists('admission_payments');
    }
};
