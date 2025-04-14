<?php

namespace App\Notifications;

use App\Models\LearningSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SessionReminder extends Notification implements ShouldQueue
{
    use Queueable;

    protected $session;
    protected $type;

    /**
     * Create a new notification instance.
     */
    public function __construct(LearningSession $session, string $type = 'reminder')
    {
        $this->session = $session;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject($this->getSubject());

        if ($this->type === 'cancelled') {
            $message->line('The following session has been cancelled:')
                   ->line('Title: ' . $this->session->title)
                   ->line('Originally scheduled for: ' . $this->session->start_time->format('D, M j, Y g:i A'));
        } else {
            $message->line('Reminder: Your learning session starts in ' . $this->session->reminder_minutes . ' minutes')
                   ->line('Title: ' . $this->session->title)
                   ->line('Time: ' . $this->session->start_time->format('D, M j, Y g:i A'));

            if ($this->session->mode === 'online') {
                $message->action('Join Meeting', $this->session->meeting_link);
            } else {
                $message->line('Location: ' . $this->session->location);
            }
        }

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'session_id' => $this->session->id,
            'title' => $this->session->title,
            'start_time' => $this->session->start_time,
            'type' => $this->type,
            'mode' => $this->session->mode,
            'meeting_link' => $this->session->meeting_link,
            'location' => $this->session->location,
        ];
    }

    protected function getSubject(): string
    {
        return match($this->type) {
            'cancelled' => 'Session Cancelled: ' . $this->session->title,
            default => 'Session Reminder: ' . $this->session->title,
        };
    }
}
