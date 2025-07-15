<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Analytics Report - {{ $reportData['period']['start'] ?? 'N/A' }} to {{ $reportData['period']['end'] ?? 'N/A' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            line-height: 1.6;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #4F46E5;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #4F46E5;
            margin: 0;
            font-size: 28px;
        }
        
        .header .subtitle {
            color: #6B7280;
            font-size: 14px;
            margin-top: 5px;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .summary-card {
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
        }
        
        .summary-card h3 {
            margin: 0;
            font-size: 24px;
            color: #4F46E5;
            font-weight: bold;
        }
        
        .summary-card p {
            margin: 5px 0 0 0;
            color: #6B7280;
            font-size: 14px;
        }
        
        .section {
            margin-bottom: 40px;
            page-break-inside: avoid;
        }
        
        .section h2 {
            color: #1F2937;
            border-bottom: 2px solid #E5E7EB;
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-size: 20px;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .data-table th,
        .data-table td {
            border: 1px solid #E5E7EB;
            padding: 12px;
            text-align: left;
        }
        
        .data-table th {
            background-color: #F3F4F6;
            font-weight: bold;
            color: #374151;
        }
        
        .data-table tr:nth-child(even) {
            background-color: #F9FAFB;
        }
        
        .metric-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 10px;
            background: #F8FAFC;
            border-radius: 6px;
        }
        
        .metric-label {
            font-weight: 600;
            color: #374151;
        }
        
        .metric-value {
            font-weight: bold;
            color: #4F46E5;
        }
        
        .success-rate {
            color: #10B981;
        }
        
        .warning-rate {
            color: #F59E0B;
        }
        
        .danger-rate {
            color: #EF4444;
        }
        
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #E5E7EB;
            text-align: center;
            color: #6B7280;
            font-size: 12px;
        }
        
        .risk-alert {
            background: #FEF2F2;
            border: 1px solid #FECACA;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .risk-alert h4 {
            color: #DC2626;
            margin: 0 0 10px 0;
            font-size: 16px;
        }
        
        .risk-alert p {
            margin: 0;
            color: #7F1D1D;
        }
        
        .chart-placeholder {
            background: #F3F4F6;
            border: 2px dashed #D1D5DB;
            border-radius: 8px;
            padding: 40px;
            text-align: center;
            color: #6B7280;
            margin: 20px 0;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        @media print {
            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>OKR Analytics Report</h1>
        <div class="subtitle">
            Generated on {{ now()->format('F j, Y \a\t g:i A') }}<br>
            Period: {{ $reportData['period']['start'] ?? 'N/A' }} to {{ $reportData['period']['end'] ?? 'N/A' }}
        </div>
    </div>

    <!-- Executive Summary -->
    <div class="summary-grid">
        <div class="summary-card">
            <h3>{{ $reportData['company']['success_rate'] ?? '0' }}%</h3>
            <p>Overall Success Rate</p>
        </div>
        <div class="summary-card">
            <h3>{{ $reportData['company']['total_objectives'] ?? '0' }}</h3>
            <p>Total Objectives</p>
        </div>
        <div class="summary-card">
            <h3>{{ $reportData['company']['completed_objectives'] ?? '0' }}</h3>
            <p>Completed Objectives</p>
        </div>
        <div class="summary-card">
            <h3>{{ number_format($reportData['company']['avg_completion_time'] ?? 0, 1) }}</h3>
            <p>Avg Completion Days</p>
        </div>
    </div>

    <!-- Company Performance Section -->
    <div class="section">
        <h2>Company Performance Overview</h2>
        
        <div class="metric-row">
            <span class="metric-label">Success Rate</span>
            <span class="metric-value {{ ($reportData['company']['success_rate'] ?? 0) >= 80 ? 'success-rate' : (($reportData['company']['success_rate'] ?? 0) >= 60 ? 'warning-rate' : 'danger-rate') }}">
                {{ $reportData['company']['success_rate'] ?? '0' }}%
            </span>
        </div>
        
        <div class="metric-row">
            <span class="metric-label">Total Objectives</span>
            <span class="metric-value">{{ $reportData['company']['total_objectives'] ?? '0' }}</span>
        </div>
        
        <div class="metric-row">
            <span class="metric-label">Completed Objectives</span>
            <span class="metric-value">{{ $reportData['company']['completed_objectives'] ?? '0' }}</span>
        </div>
        
        <div class="metric-row">
            <span class="metric-label">Average Completion Time</span>
            <span class="metric-value">{{ number_format($reportData['company']['avg_completion_time'] ?? 0, 1) }} days</span>
        </div>
    </div>

    <!-- Department Performance Section -->
    <div class="section">
        <h2>Department Performance Analysis</h2>
        
        @if(isset($reportData['departments']) && count($reportData['departments']) > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Department</th>
                        <th>Team Size</th>
                        <th>Total Objectives</th>
                        <th>Completed</th>
                        <th>Success Rate</th>
                        <th>Performance Rating</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportData['departments'] as $department)
                    <tr>
                        <td><strong>{{ $department['department'] ?? 'Unknown' }}</strong></td>
                        <td>{{ $department['team_size'] ?? '0' }}</td>
                        <td>{{ $department['total_objectives'] ?? '0' }}</td>
                        <td>{{ $department['completed_objectives'] ?? '0' }}</td>
                        <td class="{{ ($department['success_rate'] ?? 0) >= 80 ? 'success-rate' : (($department['success_rate'] ?? 0) >= 60 ? 'warning-rate' : 'danger-rate') }}">
                            {{ number_format($department['success_rate'] ?? 0, 1) }}%
                        </td>
                        <td>
                            @if(($department['success_rate'] ?? 0) >= 90)
                                <span style="color: #10B981;">Excellent</span>
                            @elseif(($department['success_rate'] ?? 0) >= 80)
                                <span style="color: #059669;">Good</span>
                            @elseif(($department['success_rate'] ?? 0) >= 70)
                                <span style="color: #D97706;">Fair</span>
                            @elseif(($department['success_rate'] ?? 0) >= 60)
                                <span style="color: #DC2626;">Poor</span>
                            @else
                                <span style="color: #991B1B;">Critical</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No department data available for this period.</p>
        @endif
    </div>

    <!-- Top Performers Section -->
    <div class="section">
        <h2>Top Performers</h2>
        
        @if(isset($reportData['top_performers']) && count($reportData['top_performers']) > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Name</th>
                        <th>Total Objectives</th>
                        <th>Completed</th>
                        <th>Success Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportData['top_performers']->take(10) as $index => $performer)
                    <tr>
                        <td><strong>{{ $index + 1 }}</strong></td>
                        <td>{{ $performer['name'] ?? 'Unknown' }}</td>
                        <td>{{ $performer['total_objectives'] ?? '0' }}</td>
                        <td>{{ $performer['completed_objectives'] ?? '0' }}</td>
                        <td class="success-rate">{{ number_format($performer['success_rate'] ?? 0, 1) }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No performer data available for this period.</p>
        @endif
    </div>

    <!-- Risk Analysis Section -->
    @if(isset($alerts) && count($alerts) > 0)
    <div class="section page-break">
        <h2>Risk Analysis & Alerts</h2>
        
        @foreach($alerts->take(10) as $alert)
        <div class="risk-alert">
            <h4>{{ $alert['type'] === 'overdue' ? '⚠️ Overdue Objective' : '🚨 At-Risk Objective' }}</h4>
            <p><strong>{{ $alert['title'] ?? 'Unknown Objective' }}</strong></p>
            <p>{{ $alert['message'] ?? 'No details available' }}</p>
            <p><small>Completion Probability: {{ $alert['probability'] ?? '0' }}%</small></p>
            @if(isset($alert['recommendations']) && count($alert['recommendations']) > 0)
                <p><small><strong>Recommendations:</strong> {{ implode('; ', $alert['recommendations']) }}</small></p>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    <!-- Key Insights Section -->
    <div class="section">
        <h2>Key Insights & Recommendations</h2>
        
        <div style="background: #EBF8FF; border: 1px solid #BEE3F8; border-radius: 6px; padding: 20px; margin-bottom: 20px;">
            <h4 style="color: #2B6CB0; margin: 0 0 10px 0;">📊 Performance Insights</h4>
            <ul style="margin: 0; padding-left: 20px; color: #2C5282;">
                <li>Company success rate: <strong>{{ $reportData['company']['success_rate'] ?? '0' }}%</strong></li>
                @if(isset($reportData['departments']) && count($reportData['departments']) > 0)
                    @php
                        $bestDept = collect($reportData['departments'])->sortByDesc('success_rate')->first();
                        $worstDept = collect($reportData['departments'])->sortBy('success_rate')->first();
                    @endphp
                    <li>Best performing department: <strong>{{ $bestDept['department'] ?? 'N/A' }}</strong> ({{ number_format($bestDept['success_rate'] ?? 0, 1) }}%)</li>
                    <li>Area for improvement: <strong>{{ $worstDept['department'] ?? 'N/A' }}</strong> ({{ number_format($worstDept['success_rate'] ?? 0, 1) }}%)</li>
                @endif
                <li>Average completion time: <strong>{{ number_format($reportData['company']['avg_completion_time'] ?? 0, 1) }} days</strong></li>
            </ul>
        </div>

        <div style="background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 6px; padding: 20px;">
            <h4 style="color: #166534; margin: 0 0 10px 0;">💡 Actionable Recommendations</h4>
            <ul style="margin: 0; padding-left: 20px; color: #15803D;">
                @if(($reportData['company']['success_rate'] ?? 0) < 70)
                    <li>Focus on improving overall success rate through better goal setting and tracking</li>
                @endif
                @if(($reportData['company']['avg_completion_time'] ?? 0) > 30)
                    <li>Consider breaking down objectives into smaller, more manageable tasks</li>
                @endif
                <li>Implement regular check-ins with underperforming departments</li>
                <li>Share best practices from top-performing teams across the organization</li>
                <li>Provide additional support and resources to teams with success rates below 60%</li>
                @if(isset($alerts) && count($alerts) > 0)
                    <li>Address {{ count($alerts) }} at-risk objectives immediately to prevent missed deadlines</li>
                @endif
            </ul>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>This report was automatically generated by the OKR Analytics System</p>
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A T') }}</p>
        <p>For questions or support, contact your system administrator</p>
    </div>
</body>
</html> 