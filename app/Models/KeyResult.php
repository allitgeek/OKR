<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KeyResult extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'objective_id',
        'owner_id',
        'target_value',
        'current_value',
        'metric_unit',
        'progress',
        'status',
        'start_date',
        'due_date',
    ];

    protected $casts = [
        'target_value' => 'decimal:2',
        'current_value' => 'decimal:2',
        'progress' => 'integer',
        'start_date' => 'date',
        'due_date' => 'date',
    ];

    public function objective(): BelongsTo
    {
        return $this->belongsTo(Objective::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function calculateProgress(): void
    {
        if ($this->target_value == 0) {
            $this->progress = 0;
        } else {
            $this->progress = (int) round(($this->current_value / $this->target_value) * 100);
            // Ensure progress stays within bounds
            $this->progress = max(0, min(100, $this->progress));
        }

        $this->saveQuietly();
        $this->objective->calculateProgressWithoutEvents();
    }

    public function calculateProgressWithoutEvents(): void
    {
        if ($this->target_value == 0) {
            $this->progress = 0;
        } else {
            $this->progress = (int) round(($this->current_value / $this->target_value) * 100);
            // Ensure progress stays within bounds
            $this->progress = max(0, min(100, $this->progress));
        }

        // Use a transaction to prevent any potential race conditions
        \DB::transaction(function () {
            $this->saveQuietly();
        });
    }

    protected static function booted()
    {
        parent::boot();

        static::saved(function ($keyResult) {
            // Update status based on progress without triggering more events
            if ($keyResult->progress == 100 && $keyResult->status != 'completed') {
                $keyResult->status = 'completed';
                $keyResult->saveQuietly();
            } elseif ($keyResult->progress > 0 && $keyResult->progress < 100 && $keyResult->status == 'not_started') {
                $keyResult->status = 'in_progress';
                $keyResult->saveQuietly();
            }

            // Update objective progress without triggering more events
            if (!$keyResult->objective->isUpdatingProgress) {
                $keyResult->objective->calculateProgressWithoutEvents();
            }
        });

        static::deleted(function ($keyResult) {
            $keyResult->objective->calculateProgressWithoutEvents();
        });
    }
}
