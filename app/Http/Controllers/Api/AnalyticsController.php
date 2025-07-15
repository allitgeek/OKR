<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AnalyticsController extends Controller
{
    private AnalyticsService $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Get dashboard metrics for the main analytics overview
     */
    public function dashboard(): JsonResponse
    {
        try {
            $metrics = $this->analyticsService->getDashboardMetrics();
            
            return response()->json([
                'success' => true,
                'data' => $metrics
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard metrics',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get success rate analytics for specified period
     */
    public function successRates(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        try {
            $startDate = $request->start_date ? Carbon::parse($request->start_date) : null;
            $endDate = $request->end_date ? Carbon::parse($request->end_date) : null;

            $data = $this->analyticsService->generateSuccessRateMetrics($startDate, $endDate);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch success rate metrics',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get team performance heatmap data
     */
    public function teamPerformance(Request $request): JsonResponse
    {
        $request->validate([
            'team_identifier' => 'nullable|string',
        ]);

        try {
            $data = $this->analyticsService->calculateTeamPerformance($request->team_identifier);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch team performance data',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get predictive insights for objectives
     */
    public function predictiveInsights(Request $request): JsonResponse
    {
        $request->validate([
            'objective_id' => 'nullable|exists:objectives,id',
        ]);

        try {
            $data = $this->analyticsService->getPredictiveInsights($request->objective_id);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch predictive insights',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get trending data for charts
     */
    public function trends(Request $request): JsonResponse
    {
        $request->validate([
            'metric_type' => 'required|string|in:success_rate,completion_time,engagement,objective_count',
            'period' => 'nullable|integer|min:7|max:365',
        ]);

        try {
            $period = $request->period ?? 30;
            $data = $this->analyticsService->getTrendingData($request->metric_type, $period);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch trending data',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Generate custom report
     */
    public function customReport(Request $request): JsonResponse
    {
        $request->validate([
            'components' => 'required|array',
            'components.*.id' => 'required|string',
            'components.*.type' => 'required|string|in:success_rate_chart,team_comparison,objective_timeline,performance_table',
            'components.*.parameters' => 'nullable|array',
        ]);

        try {
            $config = $request->only(['components']);
            $report = $this->analyticsService->generateCustomReport($config);

            return response()->json([
                'success' => true,
                'data' => $report
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate custom report',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get performance comparison data
     */
    public function performanceComparison(Request $request): JsonResponse
    {
        $request->validate([
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
            'departments' => 'nullable|array',
            'departments.*' => 'string',
            'period' => 'nullable|string|in:week,month,quarter,year',
        ]);

        try {
            $period = $request->period ?? 'quarter';
            $endDate = Carbon::now();
            
            switch ($period) {
                case 'week':
                    $startDate = $endDate->copy()->subWeek();
                    break;
                case 'month':
                    $startDate = $endDate->copy()->subMonth();
                    break;
                case 'year':
                    $startDate = $endDate->copy()->subYear();
                    break;
                default:
                    $startDate = $endDate->copy()->startOfQuarter();
                    break;
            }

            $data = $this->analyticsService->generateSuccessRateMetrics($startDate, $endDate);

            // Filter by requested users/departments if provided
            if ($request->users) {
                $data['top_performers'] = $data['top_performers']->whereIn('user_id', $request->users);
            }

            if ($request->departments) {
                $data['departments'] = $data['departments']->whereIn('department', $request->departments);
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch performance comparison data',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Export analytics data
     */
    public function export(Request $request): JsonResponse|Response
    {
        $request->validate([
            'type' => 'required|string|in:dashboard,success_rates,team_performance,predictive_insights',
            'format' => 'required|string|in:json,csv,excel,pdf',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        try {
            $data = null;
            $startDate = $request->start_date ? Carbon::parse($request->start_date) : null;
            $endDate = $request->end_date ? Carbon::parse($request->end_date) : null;

            switch ($request->type) {
                case 'dashboard':
                    $data = $this->analyticsService->getDashboardMetrics();
                    break;
                case 'success_rates':
                    $data = $this->analyticsService->generateSuccessRateMetrics($startDate, $endDate);
                    break;
                case 'team_performance':
                    $data = $this->analyticsService->calculateTeamPerformance();
                    break;
                case 'predictive_insights':
                    $data = $this->analyticsService->getPredictiveInsights();
                    break;
            }

            // Handle PDF export
            if ($request->format === 'pdf') {
                return $this->generatePdfReport($data, $request->type);
            }

            // Handle CSV export
            if ($request->format === 'csv') {
                return $this->generateCsvReport($data, $request->type);
            }

            // Handle Excel export (placeholder for now)
            if ($request->format === 'excel') {
                return response()->json([
                    'success' => false,
                    'message' => 'Excel export not yet implemented'
                ], 501);
            }

            // Default JSON export
            return response()->json([
                'success' => true,
                'data' => $data,
                'export_info' => [
                    'type' => $request->type,
                    'format' => $request->format,
                    'generated_at' => Carbon::now(),
                    'filename' => "analytics_{$request->type}_" . Carbon::now()->format('Y-m-d_H-i-s') . ".{$request->format}"
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export analytics data',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Generate PDF report
     */
    private function generatePdfReport($data, string $type): Response
    {
        try {
            // Get additional data for comprehensive report
            $alerts = $this->analyticsService->getPredictiveInsights()
                ->filter(function($insight) {
                    return $insight['risk_level'] === 'high' || 
                           in_array('overdue', $insight['risk_factors']);
                });

            $pdf = Pdf::loadView('analytics.pdf.report', [
                'reportData' => $data,
                'alerts' => $alerts,
                'reportType' => $type
            ]);

            $filename = "analytics_report_" . Carbon::now()->format('Y-m-d_H-i-s') . ".pdf";

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate PDF report',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Generate CSV report
     */
    private function generateCsvReport($data, string $type): Response
    {
        try {
            $filename = "analytics_{$type}_" . Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function() use ($data, $type) {
                $file = fopen('php://output', 'w');
                
                switch ($type) {
                    case 'success_rates':
                        $this->exportSuccessRatesCsv($file, $data);
                        break;
                    case 'team_performance':
                        $this->exportTeamPerformanceCsv($file, $data);
                        break;
                    case 'dashboard':
                        $this->exportDashboardCsv($file, $data);
                        break;
                    default:
                        fputcsv($file, ['Type', 'Value']);
                        fputcsv($file, ['Export Type', $type]);
                        fputcsv($file, ['Generated At', Carbon::now()->toString()]);
                        break;
                }
                
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate CSV report',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    private function exportSuccessRatesCsv($file, $data)
    {
        // Company overview
        fputcsv($file, ['Company Overview']);
        fputcsv($file, ['Metric', 'Value']);
        fputcsv($file, ['Success Rate', ($data['company']['success_rate'] ?? 0) . '%']);
        fputcsv($file, ['Total Objectives', $data['company']['total_objectives'] ?? 0]);
        fputcsv($file, ['Completed Objectives', $data['company']['completed_objectives'] ?? 0]);
        fputcsv($file, ['Avg Completion Time', ($data['company']['avg_completion_time'] ?? 0) . ' days']);
        fputcsv($file, []);

        // Department performance
        if (isset($data['departments']) && count($data['departments']) > 0) {
            fputcsv($file, ['Department Performance']);
            fputcsv($file, ['Department', 'Team Size', 'Total Objectives', 'Completed', 'Success Rate']);
            
            foreach ($data['departments'] as $dept) {
                fputcsv($file, [
                    $dept['department'] ?? 'Unknown',
                    $dept['team_size'] ?? 0,
                    $dept['total_objectives'] ?? 0,
                    $dept['completed_objectives'] ?? 0,
                    ($dept['success_rate'] ?? 0) . '%'
                ]);
            }
            fputcsv($file, []);
        }

        // Top performers
        if (isset($data['top_performers']) && count($data['top_performers']) > 0) {
            fputcsv($file, ['Top Performers']);
            fputcsv($file, ['Name', 'Total Objectives', 'Completed', 'Success Rate']);
            
            foreach ($data['top_performers']->take(10) as $performer) {
                fputcsv($file, [
                    $performer['name'] ?? 'Unknown',
                    $performer['total_objectives'] ?? 0,
                    $performer['completed_objectives'] ?? 0,
                    ($performer['success_rate'] ?? 0) . '%'
                ]);
            }
        }
    }

    private function exportTeamPerformanceCsv($file, $data)
    {
        fputcsv($file, ['Team Performance Analysis']);
        fputcsv($file, ['Department', 'Member', 'Objectives', 'Completed', 'Success Rate', 'Last Activity']);
        
        foreach ($data as $department => $members) {
            foreach ($members as $member) {
                fputcsv($file, [
                    $department,
                    $member['name'] ?? 'Unknown',
                    $member['total_objectives'] ?? 0,
                    $member['completed_objectives'] ?? 0,
                    ($member['success_rate'] ?? 0) . '%',
                    $member['last_activity'] ? Carbon::parse($member['last_activity'])->format('Y-m-d H:i') : 'N/A'
                ]);
            }
        }
    }

    private function exportDashboardCsv($file, $data)
    {
        fputcsv($file, ['Dashboard Metrics']);
        fputcsv($file, ['Metric', 'Value']);
        fputcsv($file, ['Success Rate', ($data['success_rate'] ?? 0) . '%']);
        fputcsv($file, ['Success Rate Trend', ($data['success_rate_trend'] ?? 0) . '%']);
        fputcsv($file, ['Total Objectives', $data['total_objectives'] ?? 0]);
        fputcsv($file, ['Completed Objectives', $data['completed_objectives'] ?? 0]);
        fputcsv($file, ['Overdue Objectives', $data['overdue_objectives'] ?? 0]);
        fputcsv($file, ['Active Users', $data['active_users'] ?? 0]);
        fputcsv($file, ['Objectives Due Today', $data['objectives_due_today'] ?? 0]);
        fputcsv($file, ['At Risk Count', $data['at_risk_count'] ?? 0]);
    }

    /**
     * Get real-time alerts and notifications
     */
    public function alerts(): JsonResponse
    {
        try {
            $insights = $this->analyticsService->getPredictiveInsights();
            
            // Filter for high-risk items that need immediate attention
            $alerts = $insights->filter(function($insight) {
                return $insight['risk_level'] === 'high' || 
                       in_array('overdue', $insight['risk_factors']);
            })->map(function($insight) {
                return [
                    'id' => $insight['objective_id'],
                    'title' => $insight['title'],
                    'type' => in_array('overdue', $insight['risk_factors']) ? 'overdue' : 'at_risk',
                    'message' => $this->generateAlertMessage($insight),
                    'probability' => $insight['completion_probability'],
                    'recommendations' => $insight['recommendations']
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'alerts' => $alerts->values(),
                    'total_count' => $alerts->count(),
                    'generated_at' => Carbon::now()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch alerts',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Generate alert message based on insight data
     */
    private function generateAlertMessage($insight): string
    {
        if (in_array('overdue', $insight['risk_factors'])) {
            return "Objective '{$insight['title']}' is overdue and needs immediate attention.";
        }
        
        if ($insight['completion_probability'] < 30) {
            return "Objective '{$insight['title']}' has only a {$insight['completion_probability']}% chance of completion.";
        }
        
        return "Objective '{$insight['title']}' is at risk and may need support.";
    }
} 