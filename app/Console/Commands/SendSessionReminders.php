<?php

namespace App\Console\Commands;

use App\Models\LearningSession;
use App\Models\SessionReminder;
use App\Notifications\SessionReminder as SessionReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendSessionReminders extends Command
{
    protected $signature = 'sessions:send-reminders';
    protected $description = 'Send reminders for upcoming learning sessions';

    public function handle()
    {
        $now = Carbon::now();
        
        // Get all upcoming sessions within the next hour
        $upcomingSessions = LearningSession::where('start_time', '>', $now)
            ->where('start_time', '<=', $now->copy()->addHour())
            ->with(['participants', 'reminders.user'])
            ->get();

        foreach ($upcomingSessions as $session) {
            foreach ($session->reminders as $reminder) {
                $reminderTime = $session->start_time->copy()->subMinutes($reminder->minutes_before);
                
                // If it's time to send the reminder (within the last minute)
                if ($now->diffInMinutes($reminderTime, false) <= 0 && $now->diffInMinutes($reminderTime) <= 1) {
                    $user = $reminder->user;
                    
                    if ($reminder->email_notification) {
                        $user->notify(new SessionReminderNotification($session));
                    }

                    // For browser notifications, we'll store in database
                    // and let the frontend poll for new notifications
                    if ($reminder->browser_notification) {
                        $user->notifications()->create([
                            'type' => SessionReminderNotification::class,
                            'data' => [
                                'session_id' => $session->id,
                                'title' => $session->title,
                                'start_time' => $session->start_time,
                                'type' => 'reminder',
                                'mode' => $session->mode,
                                'meeting_link' => $session->meeting_link,
                                'location' => $session->location,
                            ]
                        ]);
                    }
                }
            }
        }

        $this->info('Session reminders sent successfully.');
    }
}
