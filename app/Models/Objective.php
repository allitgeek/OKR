<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Objective extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'user_id',
        'team_id',
        'creator_id',
        'start_date',
        'end_date',
        'status',
        'time_period',
        'progress',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'progress' => 'integer',
    ];

    public $isUpdatingProgress = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function keyResults(): HasMany
    {
        return $this->hasMany(KeyResult::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    protected static function booted()
    {
        static::saved(function ($objective) {
            // Update status based on progress without triggering more events
            if ($objective->progress == 100 && $objective->status != 'completed') {
                $objective->status = 'completed';
                $objective->saveQuietly();
            } elseif ($objective->progress > 0 && $objective->progress < 100 && $objective->status == 'not_started') {
                $objective->status = 'in_progress';
                $objective->saveQuietly();
            }
        });
    }

    public function updateProgress()
    {
        if ($this->isUpdatingProgress) {
            return;
        }

        $this->isUpdatingProgress = true;
        
        try {
            if ($this->keyResults()->count() > 0) {
                $totalProgress = $this->keyResults->sum(function ($keyResult) {
                    return ($keyResult->current_value / $keyResult->target_value) * 100;
                });
                $this->progress = round($totalProgress / $this->keyResults()->count());
            } else {
                $this->progress = 0;
            }
            
            $this->saveQuietly();
        } finally {
            $this->isUpdatingProgress = false;
        }
    }

    public function calculateProgressWithoutEvents()
    {
        if ($this->isUpdatingProgress) {
            return;
        }

        $this->isUpdatingProgress = true;
        
        try {
            if ($this->keyResults()->count() > 0) {
                $totalProgress = $this->keyResults->sum(function ($keyResult) {
                    return ($keyResult->current_value / $keyResult->target_value) * 100;
                });
                $this->progress = round($totalProgress / $this->keyResults()->count());
            } else {
                $this->progress = 0;
            }
            
            $this->saveQuietly();
        } finally {
            $this->isUpdatingProgress = false;
        }
    }
}
