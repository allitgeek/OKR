<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AnalyticsSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'snapshot_date',
        'metric_type',
        'metric_value',
        'entity_type',
        'entity_id',
        'metadata'
    ];

    protected $casts = [
        'snapshot_date' => 'date',
        'metric_value' => 'decimal:2',
        'metadata' => 'array'
    ];

    // Scopes for easy querying
    public function scopeForPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('snapshot_date', [$startDate, $endDate]);
    }

    public function scopeOfType($query, $metricType)
    {
        return $query->where('metric_type', $metricType);
    }

    public function scopeForEntity($query, $entityType, $entityId = null)
    {
        $query = $query->where('entity_type', $entityType);
        
        if ($entityId !== null) {
            $query->where('entity_id', $entityId);
        }
        
        return $query;
    }

    // Static methods for common operations
    public static function recordMetric($date, $type, $value, $entityType, $entityId = null, $metadata = [])
    {
        return static::create([
            'snapshot_date' => $date,
            'metric_type' => $type,
            'metric_value' => $value,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'metadata' => $metadata
        ]);
    }

    public static function getMetricTrend($metricType, $entityType, $entityId = null, $days = 30)
    {
        return static::ofType($metricType)
            ->forEntity($entityType, $entityId)
            ->forPeriod(Carbon::now()->subDays($days), Carbon::now())
            ->orderBy('snapshot_date')
            ->get();
    }
} 