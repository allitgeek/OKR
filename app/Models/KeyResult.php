<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class KeyResult extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'objective_id',
        'title',
        'description',
        'initial_value',
        'target_value',
        'current_value',
        'weight',
        'confidence',
        'status',
        'type',
        'assignee_id',
    ];

    protected $casts = [
        'target_value' => 'decimal:2',
        'current_value' => 'decimal:2',
        'progress' => 'integer',
        'start_date' => 'date',
        'due_date' => 'date',
        'okr_score' => 'decimal:2',
        'confidence_level' => 'decimal:2',
        'last_check_in' => 'datetime',
        'is_measurable' => 'boolean',
        'is_time_bound' => 'boolean',
    ];

    public function objective(): BelongsTo
    {
        return $this->belongsTo(Objective::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function checkIns(): HasMany
    {
        return $this->hasMany(OkrCheckIn::class);
    }

    public function calculateProgress(): void
    {
        if ($this->target_value == 0) {
            $this->progress = $this->current_value == 0 ? 100 : 0;
        } else {
            $this->progress = (int) round(($this->current_value / $this->target_value) * 100);
            $this->progress = max(0, min(100, $this->progress));
        }

        $this->saveQuietly();
        
        // Also calculate OKR score
        $this->calculateOkrScore();
        
        // Update objective
        $this->objective->calculateProgressWithoutEvents();
        $this->objective->calculateOkrScore();
    }

    public function calculateProgressWithoutEvents(): void
    {
        if ($this->target_value == 0) {
            // For zero targets, if current value is also 0, it's 100% complete
            $this->progress = $this->current_value == 0 ? 100 : 0;
        } else {
            $this->progress = (int) round(($this->current_value / $this->target_value) * 100);
            // Ensure progress stays within bounds
            $this->progress = max(0, min(100, $this->progress));
        }

        // Use a transaction to prevent any potential race conditions
        DB::transaction(function () {
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

    // OKR Methodology Methods
    public function calculateOkrScore(): float
    {
        if ($this->target_value == 0) {
            // Binary Key Results (milestone type)
            $score = $this->current_value == 0 ? 1.0 : 0.0;
        } else {
            // Calculate score based on KR type
            $ratio = $this->current_value / $this->target_value;
            
            switch ($this->kr_type) {
                case 'positive':
                    $score = min(1.0, $ratio); // More is better
                    break;
                case 'negative':
                    $score = max(0.0, 2.0 - $ratio); // Less is better (inverse)
                    break;
                case 'baseline':
                    $score = abs($ratio - 1.0) <= 0.1 ? 1.0 : max(0.0, 1.0 - abs($ratio - 1.0)); // Maintain baseline
                    break;
                case 'milestone':
                    $score = $ratio >= 1.0 ? 1.0 : 0.0; // All or nothing
                    break;
                default:
                    $score = min(1.0, $ratio);
            }
        }

        $this->okr_score = round($score, 2);
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
        return OkrCheckIn::createForKeyResult($this, $data);
    }

    public function getLatestCheckIn(): ?OkrCheckIn
    {
        return OkrCheckIn::getLatestForKeyResult($this);
    }

    public function getKrTypeDescription(): string
    {
        return match($this->kr_type) {
            'positive' => 'Increase/Improve (more is better)',
            'negative' => 'Decrease/Reduce (less is better)',
            'baseline' => 'Maintain (keep at target level)',
            'milestone' => 'Complete/Launch (binary achievement)',
            default => 'Standard metric'
        };
    }



    // Scopes for OKR methodology
    public function scopeByType($query, $type)
    {
        return $query->where('kr_type', $type);
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
