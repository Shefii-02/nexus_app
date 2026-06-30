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


        Schema::create('teacher_payment_items', function (Blueprint $table) {

            $table->id();

            $table->foreignId('teacher_id')
                ->constrained('users');

            $table->foreignId('course_id')
                ->nullable()
                ->constrained('courses');

            $table->string('month');

            $table->enum('calculation_type', [
                'monthly_salary',
                'revenue_share',
                'per_student',
                'fixed_amount',
                'referral',
                'custom'
            ]);

            $table->integer('student_count')
                ->default(0);

            $table->decimal('course_revenue', 12, 2)
                ->default(0);

            $table->decimal('share_percentage', 5, 2)
                ->default(0);

            $table->decimal('amount', 12, 2);

            $table->text('remarks')
                ->nullable();

            $table->enum('status', [
                'pending',
                'released',
                'cancelled'
            ])->default('pending');

            $table->foreignId('created_by')
                ->constrained('users');

            $table->timestamps();

            $table->softDeletes();
        });

        Schema::create('teacher_payments', function (Blueprint $table) {

            $table->id();

            $table->foreignId('teacher_id')
                ->constrained('users');

            $table->date('period_start');

            $table->date('period_end');

            $table->integer('total_classes')
                ->default(0)->nullable();

            $table->decimal('gross_amount', 12, 2);

            $table->decimal('deduction_amount', 12, 2)
                ->default(0);

            $table->text('deduction_reason')->nullable();


            $table->decimal('amount', 12, 2);

            $table->string('payment_method')->nullable();

            $table->string('payment_reference')
                ->nullable();

            $table->string('transaction_no')
                ->nullable();

            $table->date('payment_date')->nullable();

            $table->text('remarks')
                ->nullable();

            $table->foreignId('created_by')->nullable()
                ->constrained('users');

            $table->foreignId('released_by')->nullable()
                ->constrained('users');

            $table->enum('status', [
                'pending',
                'released',
                'cancelled'
            ])->default('pending');

            $table->timestamp('paid_at')
                ->nullable();

            $table->timestamps();

            $table->softDeletes();
        });


        Schema::create('teacher_payment_item_payment', function (Blueprint $table) {

            $table->id();

            $table->foreignId('teacher_payment_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('teacher_payment_item_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_payment_items');
        Schema::dropIfExists('teacher_payments');
        Schema::dropIfExists('teacher_payment_item_payment');
    }
};
