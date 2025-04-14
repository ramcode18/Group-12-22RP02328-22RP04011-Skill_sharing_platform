<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LearningSession extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'host_id',
        'start_time',
        'end_time',
        'mode',
        'location',
        'meeting_link',
        'meeting_platform',
        'tools_needed',
        'max_participants',
        'is_recurring',
        'recurrence_pattern',
        'recurrence_end_date',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'recurrence_end_date' => 'date',
        'is_recurring' => 'boolean',
        'tools_needed' => 'array',
    ];

    public function host()
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'session_participants')
            ->withPivot(['reminder_enabled', 'calendar_synced', 'calendar_event_id'])
            ->withTimestamps();
    }

    public function reminders()
    {
        return $this->hasMany(SessionReminder::class, 'session_id');
    }

    public function isHost(User $user)
    {
        return $this->host_id === $user->id;
    }

    public function canJoin(User $user)
    {
        return !$this->participants()->where('user_id', $user->id)->exists() &&
               $this->participants()->count() < $this->max_participants;
    }

    public function generateMeetingLink()
    {
        if ($this->mode !== 'online') {
            return null;
        }

        // Here we'll integrate with Zoom/Google Meet API
        // For now, we'll just create a Google Meet link as it's free and doesn't require API setup
        $meetCode = strtolower(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 3)) . 
                   substr(str_shuffle('123456789'), 0, 3);
        
        $this->meeting_platform = 'google_meet';
        $this->meeting_link = "https://meet.google.com/" . $meetCode;
        $this->save();

        return $this->meeting_link;
    }

    public function getNextOccurrence()
    {
        if (!$this->is_recurring) {
            return null;
        }

        $lastOccurrence = $this->start_time;
        $now = now();

        while ($lastOccurrence <= $this->recurrence_end_date) {
            switch ($this->recurrence_pattern) {
                case 'daily':
                    $lastOccurrence = $lastOccurrence->addDay();
                    break;
                case 'weekly':
                    $lastOccurrence = $lastOccurrence->addWeek();
                    break;
                case 'monthly':
                    $lastOccurrence = $lastOccurrence->addMonth();
                    break;
            }

            if ($lastOccurrence > $now && $lastOccurrence <= $this->recurrence_end_date) {
                return $lastOccurrence;
            }
        }

        return null;
    }
}
