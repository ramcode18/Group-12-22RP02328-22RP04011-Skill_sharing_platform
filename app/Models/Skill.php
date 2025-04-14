<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'user_id',
        'status', // shared, requested
        'category'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function reactions()
    {
        return $this->hasMany(SkillReaction::class);
    }

    public function getLikesCountAttribute()
    {
        return $this->reactions()->where('type', 'like')->count();
    }

    public function getDislikesCountAttribute()
    {
        return $this->reactions()->where('type', 'dislike')->count();
    }

    public function getUserReactionAttribute()
    {
        if (!auth()->check()) return null;
        return $this->reactions()->where('user_id', auth()->id())->value('type');
    }
}
