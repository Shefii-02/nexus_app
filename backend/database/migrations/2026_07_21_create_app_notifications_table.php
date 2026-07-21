<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('app_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type')->nullable(); // matches FcmNotificationService types: new_message, admission, announcement, material_uploaded, recorded_class, class_reminder, class_started, custom, general
            $table->string('section_id')->nullable();
            $table->string('title')->nullable();
            $table->text('body')->nullable();
            $table->json('data')->nullable(); // ids needed to rebuild the route: conversation_id, announcement_id, course_id, class_id, etc.
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'read_at']);
        });
    }
};
