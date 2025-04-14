<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'created_by'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function addMember(User $user): void
    {
        if (!$this->users->contains($user->id)) {
            $this->users()->attach($user->id);
        }
    }

    public function removeMember(User $user): void
    {
        $this->users()->detach($user->id);
    }

    public function isMember(User $user): bool
    {
        return $this->users->contains($user->id);
    }

    public function unreadMessagesForUser(User $user): int
    {
        return $this->messages()
            ->where('sender_id', '!=', $user->id)
            ->where('is_read_by_receiver', false)
            ->count();
    }
}
