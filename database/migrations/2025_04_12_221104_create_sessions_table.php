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
        Schema::create('learning_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->foreignId('host_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->enum('mode', ['online', 'offline']);
            $table->string('location')->nullable(); // Physical location for offline sessions
            $table->string('meeting_link')->nullable(); // Zoom/Google Meet link for online sessions
            $table->string('meeting_platform')->nullable(); // Zoom/Google Meet
            $table->text('tools_needed')->nullable();
            $table->integer('max_participants')->default(10);
            $table->boolean('is_recurring')->default(false);
            $table->string('recurrence_pattern')->nullable(); // daily, weekly, monthly
            $table->date('recurrence_end_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('session_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('learning_sessions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->boolean('reminder_enabled')->default(true);
            $table->boolean('calendar_synced')->default(false);
            $table->string('calendar_event_id')->nullable(); // For storing Google Calendar event ID
            $table->timestamps();
        });

        Schema::create('session_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('learning_sessions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('minutes_before')->default(15); // How many minutes before the session to send reminder
            $table->boolean('email_notification')->default(true);
            $table->boolean('browser_notification')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_reminders');
        Schema::dropIfExists('session_participants');
        Schema::dropIfExists('learning_sessions');
    }
};
