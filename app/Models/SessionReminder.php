<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessionReminder extends Model
{
    protected $fillable = [
        'session_id',
        'user_id',
        'minutes_before',
        'email_notification',
        'browser_notification'
    ];

    protected $casts = [
        'email_notification' => 'boolean',
        'browser_notification' => 'boolean'
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(LearningSession::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
