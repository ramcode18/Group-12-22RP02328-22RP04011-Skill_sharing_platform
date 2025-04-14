<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function skills()
    {
        return $this->hasMany(Skill::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function reactions()
    {
        return $this->hasMany(SkillReaction::class);
    }

    // Message relationships
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function unreadMessages()
    {
        return $this->receivedMessages()
            ->where('is_read_by_receiver', false)
            ->whereNull('parent_id');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class)
            ->withTimestamps();
    }

    public function createdGroups()
    {
        return $this->hasMany(Group::class, 'created_by');
    }

    public function hasUnreadMessagesFrom(User $sender)
    {
        return $this->receivedMessages()
            ->where('sender_id', $sender->id)
            ->where('is_read_by_receiver', false)
            ->exists();
    }

    public function markMessagesAsRead(User $sender)
    {
        return $this->receivedMessages()
            ->where('sender_id', $sender->id)
            ->where('is_read_by_receiver', false)
            ->update([
                'is_read_by_receiver' => true,
                'read_at' => now()
            ]);
    }

    public function hostedSessions()
    {
        return $this->hasMany(LearningSession::class, 'host_id');
    }

    public function participatingSessions()
    {
        return $this->belongsToMany(LearningSession::class, 'session_participants')
            ->withPivot(['reminder_enabled', 'calendar_synced', 'calendar_event_id'])
            ->withTimestamps();
    }

    public function sessionReminders()
    {
        return $this->hasMany(SessionReminder::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isLearner()
    {
        return $this->role === 'learner';
    }
}
