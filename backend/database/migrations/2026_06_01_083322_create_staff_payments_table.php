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

        Schema::create('staff_payments', function (Blueprint $table) {

            $table->id();

            $table->foreignId('staff_id')
                ->constrained('users');

            $table->string('month');
            $table->date('salary_month');
            $table->decimal('salary_amount', 12, 2);

            $table->decimal('bonus_amount', 12, 2)
                ->default(0);

            $table->decimal('deduction_amount', 12, 2)
                ->default(0);
            $table->decimal('deduction_reason', 12, 2)
                ->default(0);

            $table->decimal('final_amount', 12, 2);

            $table->enum('status', [
                'pending',
                'released'
            ])->default('pending');

            $table->timestamp('paid_at')
                ->nullable();

            $table->string('payment_method')
                ->nullable();

            $table->string('transaction_no')
                ->nullable();

            $table->date('payment_date')
                ->nullable();

            $table->text('remarks')
                ->nullable();

            $table->foreignId('released_by')
                ->nullable()
                ->constrained('users');

            $table->foreignId('created_by')
                ->constrained('users');

            $table->timestamps();

            $table->softDeletes();

            $table->unique([
                'staff_id',
                'month'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_payments');
    }
};
