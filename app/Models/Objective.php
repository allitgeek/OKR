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
        'parent_id',
        'status',
        'progress',
        'confidence_level',
        'start_date',
        'end_date',
        'company_id',
        'cycle_id',
        'cycle_year',
        'cycle_quarter',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'progress' => 'integer',
        'okr_score' => 'decimal:2',
        'confidence_level' => 'decimal:2',
        'last_check_in' => 'datetime',
        'is_measurable' => 'boolean',
        'is_specific' => 'boolean',
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

    // OKR Methodology Relationships
    public function parentObjective(): BelongsTo
    {
        return $this->belongsTo(Objective::class, 'parent_objective_id');
    }

    public function childObjectives(): HasMany
    {
        return $this->hasMany(Objective::class, 'parent_objective_id');
    }

    public function cycle(): BelongsTo
    {
        return $this->belongsTo(OkrCycle::class, 'cycle_id', 'name');
    }

    public function checkIns(): HasMany
    {
        return $this->hasMany(OkrCheckIn::class);
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
                    if ($keyResult->target_value == 0) {
                        // For zero targets, if current value is also 0, it's 100% complete
                        return $keyResult->current_value == 0 ? 100 : 0;
                    }
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
                    if ($keyResult->target_value == 0) {
                        // For zero targets, if current value is also 0, it's 100% complete
                        return $keyResult->current_value == 0 ? 100 : 0;
                    }
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

    // OKR Methodology Methods
    public function calculateOkrScore(): float
    {
        $totalWeight = $this->keyResults()->sum('weight');
        
        if ($totalWeight == 0) {
            $this->okr_score = 0;
            $this->saveQuietly();
            return 0;
        }

        $weightedScore = $this->keyResults()->get()->reduce(function ($carry, $kr) {
            return $carry + ($kr->okr_score * $kr->weight);
        }, 0);

        $this->okr_score = round($weightedScore / $totalWeight, 2);
        $this->saveQuietly();

        return $this->okr_score;
    }

    public function getOkrGrade(): string
    {
        $score = $this->okr_score ?? 0;
        
        if ($score >= 0.9) return 'A';
        if ($score >= 0.7) return 'B'; // 0.7 is considered success in OKRs
        if ($score >= 0.5) return 'C';
        if ($score >= 0.3) return 'D';
        return 'F';
    }

    public function isSuccessful(): bool
    {
        return ($this->okr_score ?? 0) >= 0.7;
    }

    public function isAspirationSuccessful(): bool
    {
        // For aspirational OKRs, 0.6-0.7 is considered good
        return $this->okr_type === 'aspirational' && ($this->okr_score ?? 0) >= 0.6;
    }

    public function getConfidenceStatus(): string
    {
        $confidence = $this->confidence_level ?? 0.5;
        
        if ($confidence >= 0.8) return 'high';
        if ($confidence >= 0.5) return 'medium';
        return 'low';
    }

    public function needsAttention(): bool
    {
        return $this->confidence_level < 0.5 || 
               ($this->okr_score !== null && $this->okr_score < 0.3);
    }

    public function createCheckIn(array $data): OkrCheckIn
    {
        return OkrCheckIn::createForObjective($this, $data);
    }

    public function getLatestCheckIn(): ?OkrCheckIn
    {
        return OkrCheckIn::getLatestForObjective($this);
    }

    // Scopes for OKR methodology
    public function scopeForCycle($query, $cycleId)
    {
        return $query->where('cycle_id', $cycleId);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('okr_type', $type);
    }

    public function scopeNeedsAttention($query)
    {
        return $query->where('confidence_level', '<', 0.5)
                    ->orWhere('okr_score', '<', 0.3);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('okr_score', '>=', 0.7);
    }
}
