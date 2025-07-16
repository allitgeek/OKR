<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class OkrCheckIn extends Model
{
    use HasFactory;

    protected $fillable = [
        'objective_id',
        'key_result_id',
        'user_id',
        'previous_progress',
        'current_progress',
        'confidence_level',
        'progress_notes',
        'challenges',
        'next_steps',
        'risk_factors',
        'check_in_type',
        'check_in_date'
    ];

    protected $casts = [
        'previous_progress' => 'decimal:2',
        'current_progress' => 'decimal:2',
        'confidence_level' => 'decimal:2',
        'risk_factors' => 'array',
        'check_in_date' => 'date'
    ];

    // Relationships
    public function objective(): BelongsTo
    {
        return $this->belongsTo(Objective::class);
    }

    public function keyResult(): BelongsTo
    {
        return $this->belongsTo(KeyResult::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeForObjective($query, $objectiveId)
    {
        return $query->where('objective_id', $objectiveId);
    }

    public function scopeForKeyResult($query, $keyResultId)
    {
        return $query->where('key_result_id', $keyResultId);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('check_in_date', '>=', Carbon::now()->subDays($days));
    }

    public function scopeByType($query, $type)
    {
        return $query->where('check_in_type', $type);
    }



    public function scopeThisWeek($query)
    {
        return $query->whereBetween('check_in_date', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ]);
    }

    // Helper methods
    public function getProgressDelta(): float
    {
        return $this->current_progress - $this->previous_progress;
    }

    public function isProgressPositive(): bool
    {
        return $this->getProgressDelta() > 0;
    }

    public function hasRisks(): bool
    {
        return !empty($this->risk_factors);
    }

    public function getConfidenceChange(): ?float
    {
        $previousCheckIn = static::where('objective_id', $this->objective_id)
            ->where('key_result_id', $this->key_result_id)
            ->where('check_in_date', '<', $this->check_in_date)
            ->orderBy('check_in_date', 'desc')
            ->first();

        if (!$previousCheckIn) {
            return null;
        }

        return $this->confidence_level - $previousCheckIn->confidence_level;
    }

    public function getConfidenceStatus(): string
    {
        if ($this->confidence_level >= 0.8) {
            return 'high';
        } elseif ($this->confidence_level >= 0.5) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    public function getRiskLevel(): string
    {
        $riskCount = count($this->risk_factors ?? []);
        $confidenceLevel = $this->confidence_level;

        if ($riskCount >= 3 || $confidenceLevel < 0.3) {
            return 'high';
        } elseif ($riskCount >= 2 || $confidenceLevel < 0.6) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    // Static methods
    public static function createForObjective(Objective $objective, array $data): self
    {
        return static::create(array_merge($data, [
            'objective_id' => $objective->id,
            'user_id' => $objective->user_id,
            'check_in_date' => $data['check_in_date'] ?? Carbon::now()
        ]));
    }

    public static function createForKeyResult(KeyResult $keyResult, array $data): self
    {
        return static::create(array_merge($data, [
            'key_result_id' => $keyResult->id,
            'objective_id' => $keyResult->objective_id,
            'user_id' => $keyResult->owner_id,
            'check_in_date' => $data['check_in_date'] ?? Carbon::now()
        ]));
    }

    public static function getLatestForObjective(Objective $objective): ?self
    {
        return static::forObjective($objective->id)
            ->orderBy('check_in_date', 'desc')
            ->first();
    }

    public static function getLatestForKeyResult(KeyResult $keyResult): ?self
    {
        return static::forKeyResult($keyResult->id)
            ->orderBy('check_in_date', 'desc')
            ->first();
    }
} 