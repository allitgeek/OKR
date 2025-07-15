<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'period_start',
        'period_end',
        'objectives_assigned',
        'objectives_completed',
        'objectives_in_progress',
        'objectives_overdue',
        'avg_completion_time_days',
        'success_rate',
        'total_comments',
        'total_attachments'
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'avg_completion_time_days' => 'decimal:2',
        'success_rate' => 'decimal:2'
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForPeriod($query, $startDate, $endDate)
    {
        return $query->where(function($q) use ($startDate, $endDate) {
            $q->whereBetween('period_start', [$startDate, $endDate])
              ->orWhereBetween('period_end', [$startDate, $endDate])
              ->orWhere(function($inner) use ($startDate, $endDate) {
                  $inner->where('period_start', '<=', $startDate)
                        ->where('period_end', '>=', $endDate);
              });
        });
    }

    public function scopeCurrentPeriod($query)
    {
        $now = now();
        $startOfQuarter = $now->copy()->startOfQuarter();
        $endOfQuarter = $now->copy()->endOfQuarter();
        
        return $query->forPeriod($startOfQuarter, $endOfQuarter);
    }

    // Computed attributes
    public function getCompletionRateAttribute()
    {
        if ($this->objectives_assigned == 0) return 0;
        return round(($this->objectives_completed / $this->objectives_assigned) * 100, 2);
    }

    public function getPerformanceGradeAttribute()
    {
        $rate = $this->success_rate;
        
        if ($rate >= 90) return 'A';
        if ($rate >= 80) return 'B';
        if ($rate >= 70) return 'C';
        if ($rate >= 60) return 'D';
        return 'F';
    }

    public function getEngagementScoreAttribute()
    {
        // Simple engagement calculation based on comments and attachments
        $totalObjectives = $this->objectives_assigned ?: 1;
        $commentsPerObjective = $this->total_comments / $totalObjectives;
        $attachmentsPerObjective = $this->total_attachments / $totalObjectives;
        
        return min(100, ($commentsPerObjective * 10) + ($attachmentsPerObjective * 5));
    }

    // Static methods for calculations
    public static function calculateForUser($userId, $startDate, $endDate)
    {
        $user = User::find($userId);
        if (!$user) return null;

        $objectives = $user->objectives()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $completed = $objectives->where('status', 'completed')->count();
        $inProgress = $objectives->where('status', 'in_progress')->count();
        $overdue = $objectives->where('due_date', '<', now())
            ->where('status', '!=', 'completed')->count();

        $totalComments = $objectives->sum(function($obj) {
            return $obj->comments()->count();
        });

        $totalAttachments = $objectives->sum(function($obj) {
            return $obj->attachments()->count();
        });

        $avgCompletionTime = $objectives->where('status', 'completed')
            ->avg(function($obj) {
                return $obj->created_at->diffInDays($obj->updated_at);
            });

        $successRate = $objectives->count() > 0 
            ? ($completed / $objectives->count()) * 100 
            : 0;

        return static::updateOrCreate(
            [
                'user_id' => $userId,
                'period_start' => $startDate,
                'period_end' => $endDate
            ],
            [
                'objectives_assigned' => $objectives->count(),
                'objectives_completed' => $completed,
                'objectives_in_progress' => $inProgress,
                'objectives_overdue' => $overdue,
                'avg_completion_time_days' => $avgCompletionTime,
                'success_rate' => $successRate,
                'total_comments' => $totalComments,
                'total_attachments' => $totalAttachments
            ]
        );
    }
} 