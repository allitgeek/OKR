<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING ANALYTICS API ENDPOINTS ===\n";

// Test the analytics service directly
$analyticsService = new \App\Services\AnalyticsService();

echo "1. Testing getDashboardMetrics():\n";
try {
    $dashboardMetrics = $analyticsService->getDashboardMetrics();
    print_r($dashboardMetrics);
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\n2. Testing calculateTeamPerformance():\n";
try {
    $teamPerformance = $analyticsService->calculateTeamPerformance();
    echo "Team Performance Data Count: " . $teamPerformance->count() . "\n";
    print_r($teamPerformance->toArray());
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\n3. Testing generateSuccessRateMetrics():\n";
try {
    $successRates = $analyticsService->generateSuccessRateMetrics();
    print_r($successRates);
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\n4. Check current objective data:\n";
$objectives = \App\Models\Objective::all();
foreach ($objectives as $obj) {
    echo "- {$obj->title}: status={$obj->status}, progress={$obj->progress}%\n";
}

echo "\n5. Check analytics snapshots:\n";
$snapshots = \App\Models\AnalyticsSnapshot::all();
echo "Total snapshots: " . $snapshots->count() . "\n";
foreach ($snapshots as $snapshot) {
    echo "- {$snapshot->metric_type}: {$snapshot->metric_value} ({$snapshot->snapshot_date})\n";
}

echo "\nDone!\n"; 