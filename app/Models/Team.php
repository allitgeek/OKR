<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'leader_id',
    ];

    public function leader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('role')->withTimestamps();
    }

    public function objectives(): HasMany
    {
        return $this->hasMany(Objective::class);
    }

    public function addMember(User $user, string $role = 'member'): void
    {
        $this->members()->attach($user->id, ['role' => $role]);
    }

    public function removeMember(User $user): void
    {
        $this->members()->detach($user->id);
    }

    public function isMember(User $user): bool
    {
        return $this->members()->where('user_id', $user->id)->exists();
    }
}
