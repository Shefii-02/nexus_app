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
        Schema::create('staff_attendances', function (Blueprint $table) {

            $table->id();

            $table->foreignId('staff_id')
                ->constrained('users');

            $table->date('attendance_date');

            $table->time('check_in')
                ->nullable();

            $table->time('check_out')
                ->nullable();

            $table->enum('status', [
                'present',
                'absent',
                'late',
                'half_day',
                'leave'
            ]);

            $table->text('remarks')
                ->nullable();

            $table->foreignId('marked_by')
                ->nullable()
                ->constrained('users');

            $table->timestamps();

            $table->unique([
                'staff_id',
                'attendance_date'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_attendances');
    }
};
