<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->string('type'); // announcement, payment, class, message, etc.
            $table->string('source')
                ->nullable();
            $table->string('title');
            $table->text('message');
            $table->string('action_url')
                ->nullable();
            $table->string('icon')
                ->nullable();
            $table->string('related_model')->nullable();
            $table->unsignedBigInteger('related_id')->nullable();
            $table->enum('priority', ['low', 'normal', 'medium', 'high'])->default('normal');
            $table->unsignedInteger(
                'total_receivers'
            )->default(0);

            $table->dateTime('scheduled_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['created_by', 'type']);
            $table->index('created_at');
        });


        Schema::create(
            'notification_target_users',
            function (Blueprint $table) {

                $table->id();

                $table->foreignId(
                    'notification_id'
                )
                    ->constrained()
                    ->cascadeOnDelete();

                $table->foreignId(
                    'receiver_id'
                )
                    ->constrained('users')
                    ->cascadeOnDelete();

                $table->text(
                    'fcm_token'
                )->nullable();

                $table->enum(
                    'fcm_status',
                    [
                        'pending',
                        'sent',
                        'delivered',
                        'failed'
                    ]
                )->default('pending');

                $table->enum(
                    'status',
                    [
                        'pending',
                        'sent',
                        'delivered',
                        'failed'
                    ]
                )->default('pending');

                $table->longText(
                    'fcm_response'
                )->nullable();

                $table->timestamp(
                    'delivered_at'
                )->nullable();

                $table->timestamp(
                    'read_at'
                )->nullable();

                $table->timestamp(
                    'clicked_at'
                )->nullable();

                $table->boolean(
                    'is_muted'
                )->default(false);

                $table->timestamps();

                $table->unique([
                    'notification_id',
                    'receiver_id'
                ]);

                $table->index([
                    'receiver_id',
                    'read_at'
                ]);
            }
        );

        Schema::create(
            'notification_logs',
            function (Blueprint $table) {

                $table->id();

                $table->foreignId(
                    'notification_id'
                )
                    ->nullable()
                    ->constrained()
                    ->nullOnDelete();

                $table->foreignId(
                    'user_id'
                )
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->string('action');

                $table->json('payload')
                    ->nullable();

                $table->timestamps();

                $table->index('action');
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('notification_target_users');
        Schema::dropIfExists('notification_logs');
    }
};
