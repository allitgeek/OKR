<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class OkrCycle extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'year',
        'quarter',
        'start_date',
        'end_date',
        'planning_start',
        'mid_quarter_review',
        'scoring_deadline',
        'status',
        'description',
        'cycle_metadata'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'planning_start' => 'date',
        'mid_quarter_review' => 'date',
        'scoring_deadline' => 'date',
        'cycle_metadata' => 'array'
    ];

    public function objectives(): HasMany
    {
        return $this->hasMany(Objective::class, 'cycle_id', 'name');
    }

    public function checkIns(): HasMany
    {
        return $this->hasMany(OkrCheckIn::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCurrent($query)
    {
        $now = Carbon::now();
        return $query->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now);
    }

    public function scopeForYear($query, $year)
    {
        return $query->where('year', $year);
    }

    public function scopeForQuarter($query, $quarter)
    {
        return $query->where('quarter', $quarter);
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCurrent(): bool
    {
        $now = Carbon::now();
        return $this->start_date <= $now && $this->end_date >= $now;
    }

    public function getDaysRemaining(): int
    {
        if ($this->end_date < Carbon::now()) {
            return 0;
        }
        return Carbon::now()->diffInDays($this->end_date, false);
    }

    public function getProgressPercentage(): float
    {
        $totalDays = $this->start_date->diffInDays($this->end_date);
        $elapsedDays = $this->start_date->diffInDays(Carbon::now());
        
        if ($totalDays === 0) return 100;
        
        return min(100, max(0, ($elapsedDays / $totalDays) * 100));
    }

    // Static methods
    public static function getCurrent(): ?self
    {
        return static::current()->first();
    }

    public static function createQuarterlySchedule(int $year): void
    {
        $quarters = [
            1 => ['start' => '01-01', 'end' => '03-31'],
            2 => ['start' => '04-01', 'end' => '06-30'],
            3 => ['start' => '07-01', 'end' => '09-30'],
            4 => ['start' => '10-01', 'end' => '12-31']
        ];

        foreach ($quarters as $quarter => $dates) {
            $startDate = Carbon::createFromFormat('Y-m-d', "$year-{$dates['start']}");
            $endDate = Carbon::createFromFormat('Y-m-d', "$year-{$dates['end']}");
            
            static::create([
                'name' => "Q{$quarter}-{$year}",
                'year' => $year,
                'quarter' => $quarter,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'planning_start' => $startDate->copy()->subWeeks(2),
                'mid_quarter_review' => $startDate->copy()->addDays(45),
                'scoring_deadline' => $endDate->copy()->addWeeks(1),
                'status' => $quarter === 1 && $year === Carbon::now()->year ? 'active' : 'planning',
                'description' => "Quarter {$quarter} {$year} OKR Cycle"
            ]);
        }
    }

    public static function getNext(): ?self
    {
        return static::where('start_date', '>', Carbon::now())
                    ->orderBy('start_date')
                    ->first();
    }
} 