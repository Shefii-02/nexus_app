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
        Schema::create('transactions', function (Blueprint $table) {

            $table->id();

            $table->enum('type', [
                'income',
                'expense',
                'refund'
            ]);

            $table->string('category');

            $table->string('reference_type')
                ->nullable();

            $table->unsignedBigInteger('reference_id')
                ->nullable();

            $table->decimal('amount', 12, 2);

            $table->string('payment_method')
                ->nullable();

            $table->string('transaction_no')
                ->nullable();

            $table->dateTime('transaction_date');

            $table->text('description')
                ->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users');

            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
