<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'sender_id',
        'receiver_id',
        'group_id',
        'parent_id',
        'type',
        'message_type',
        'priority',
        'is_private',
        'is_read_by_receiver',
        'read_at'
    ];

    protected $casts = [
        'is_private' => 'boolean',
        'is_read_by_receiver' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = ['read_status', 'formatted_content'];

    // Relationships
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function parent()
    {
        return $this->belongsTo(Message::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Message::class, 'parent_id')->where('type', 'reply');
    }

    public function comments()
    {
        return $this->hasMany(Message::class, 'parent_id')->where('type', 'comment');
    }

    // Accessors and Mutators
    public function getReadStatusAttribute()
    {
        if (!$this->is_read_by_receiver) {
            return ['status' => 'unread'];
        }

        return [
            'status' => 'read',
            'time' => $this->read_at->diffForHumans()
        ];
    }

    public function getFormattedContentAttribute()
    {
        $content = $this->content;

        // Format URLs
        $content = preg_replace('/(https?:\/\/[^\s]+)/', '<a href="$1" target="_blank" class="text-blue-600 hover:underline">$1</a>', $content);

        // Format bold text
        $content = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $content);

        // Format italic text
        $content = preg_replace('/_(.*?)_/', '<em>$1</em>', $content);

        return $content;
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('receiver_id', Auth::id())
                    ->where('is_read_by_receiver', false);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where(function($q) use ($userId) {
            $q->where('sender_id', $userId)
              ->orWhere('receiver_id', $userId);
        });
    }

    public function scopeSearch($query, $searchTerm)
    {
        return $query->where(function($q) use ($searchTerm) {
            $q->where('content', 'like', "%{$searchTerm}%")
              ->orWhereHas('sender', function($q) use ($searchTerm) {
                  $q->where('name', 'like', "%{$searchTerm}%");
              });
        });
    }

    // Helper Methods
    public function markAsRead()
    {
        if (!$this->is_read_by_receiver && $this->receiver_id === Auth::id()) {
            $this->update([
                'is_read_by_receiver' => true,
                'read_at' => now()
            ]);
        }
    }

    public function isReply()
    {
        return $this->type === 'reply';
    }

    public function isComment()
    {
        return $this->type === 'comment';
    }

    public function canBeAccessedBy($userId)
    {
        return $this->sender_id === $userId || $this->receiver_id === $userId;
    }
}
