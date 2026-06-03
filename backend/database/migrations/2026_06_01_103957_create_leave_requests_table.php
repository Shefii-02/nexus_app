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
        Schema::create('leave_requests', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained('users');

            $table->enum('user_type', [
                'teacher',
                'staff'
            ]);

            $table->date('from_date');

            $table->date('to_date');

            $table->enum('leave_type', [
                'casual',
                'sick',
                'emergency',
                'other'
            ]);

            $table->text('reason');

            $table->enum('status', [
                'pending',
                'approved',
                'rejected'
            ])->default('pending');

            $table->text('admin_remarks')
                ->nullable();

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users');

            $table->timestamp('approved_at')
                ->nullable();

            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
