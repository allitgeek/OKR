<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Analytics Dashboard') }}
            </h2>
            <div class="flex items-center space-x-4">
                <!-- Period Selector -->
                <select id="periodSelector" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                    <option value="quarter" selected>This Quarter</option>
                    <option value="year">This Year</option>
                </select>
                
                <!-- Export Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center">
                        Export Data
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    
                    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                        <div class="py-1" role="menu">
                            <button onclick="analytics.exportData('pdf')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                📄 Export as PDF
                            </button>
                            <button onclick="analytics.exportData('csv')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                📊 Export as CSV
                            </button>
                            <button onclick="analytics.exportData('json')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                📋 Export as JSON
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Loading State -->
            <div id="loadingIndicator" class="hidden">
                <div class="bg-white rounded-lg shadow p-8 text-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
                    <p class="mt-4 text-gray-600">Loading analytics data...</p>
                </div>
            </div>

            <!-- Quick Stats Bar -->
            <div id="quickStats" class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Success Rate</p>
                            <p class="text-2xl font-semibold text-gray-900">
                                <span id="successRate">--</span>%
                                <span id="successRateTrend" class="text-sm"></span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Objectives</p>
                            <p class="text-2xl font-semibold text-gray-900" id="totalObjectives">--</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Due Today</p>
                            <p class="text-2xl font-semibold text-gray-900" id="dueToday">--</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Active Users</p>
                            <p class="text-2xl font-semibold text-gray-900" id="activeUsers">--</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Dashboard Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 mb-8">
                <!-- Progress Overview Chart -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Progress Overview</h3>
                        <div class="relative">
                            <canvas id="progressChart" width="300" height="300"></canvas>
                        </div>
                        <div class="mt-4 grid grid-cols-3 gap-4 text-center text-sm">
                            <div>
                                <span class="block text-2xl font-semibold text-green-600" id="completedCount">--</span>
                                <span class="text-gray-500">Completed</span>
                            </div>
                            <div>
                                <span class="block text-2xl font-semibold text-blue-600" id="inProgressCount">--</span>
                                <span class="text-gray-500">In Progress</span>
                            </div>
                            <div>
                                <span class="block text-2xl font-semibold text-gray-600" id="notStartedCount">--</span>
                                <span class="text-gray-500">Not Started</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Success Rate Gauge -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Success Rate</h3>
                        <div class="relative">
                            <canvas id="successGauge" width="250" height="200"></canvas>
                        </div>
                        <div class="text-center mt-2">
                            <span id="gaugeValue" class="text-3xl font-bold text-indigo-600">--</span>
                            <span class="text-lg text-gray-500">%</span>
                        </div>
                    </div>
                </div>

                <!-- Team Heatmap -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Team Performance Heatmap</h3>
                        <div id="teamHeatmap" class="grid gap-2">
                            <!-- Team performance tiles will be populated here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Department Comparison Bar Chart -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Department Performance</h3>
                        <select id="departmentMetric" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="success_rate">Success Rate</option>
                            <option value="total_objectives">Total Objectives</option>
                            <option value="avg_completion_time">Avg Completion Time</option>
                        </select>
                    </div>
                    <div class="h-64">
                        <canvas id="departmentChart"></canvas>
                    </div>
                </div>

                <!-- Objective Progress Radar -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Objective Categories Analysis</h3>
                    <div class="h-64">
                        <canvas id="radarChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Trend Analysis Chart -->
            <div class="bg-white rounded-lg shadow p-6 mb-8">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Success Rate Trend</h3>
                    <select id="trendPeriod" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="30">Last 30 Days</option>
                        <option value="90">Last 3 Months</option>
                        <option value="365">Last Year</option>
                    </select>
                </div>
                <div class="h-64">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            <!-- Bottom Row: Alerts and Top Performers -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Risk Analysis & Alerts -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Risk Analysis</h3>
                    <div id="riskAlerts" class="space-y-3">
                        <!-- Risk alerts will be populated here -->
                    </div>
                    <div class="mt-4">
                        <button id="viewAllAlerts" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                            View All Alerts →
                        </button>
                    </div>
                </div>

                <!-- Top Performers -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Top Performers</h3>
                    <div id="topPerformers" class="space-y-3">
                        <!-- Top performers will be populated here -->
                    </div>
                    <div class="mt-4">
                        <button id="viewAllPerformers" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                            View Full Leaderboard →
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        class AnalyticsDashboard {
            constructor() {
                this.progressChart = null;
                this.trendChart = null;
                this.currentPeriod = 'quarter';
                this.init();
            }

            async init() {
                this.setupEventListeners();
                await this.loadDashboardData();
                this.setupCharts();
            }

            setupEventListeners() {
                document.getElementById('periodSelector').addEventListener('change', (e) => {
                    this.currentPeriod = e.target.value;
                    this.loadDashboardData();
                });

                document.getElementById('trendPeriod').addEventListener('change', (e) => {
                    this.loadTrendData(e.target.value);
                });

                // Store analytics instance globally for dropdown access
                window.analytics = this;
            }

            async loadDashboardData() {
                this.showLoading(true);
                
                try {
                    const [dashboardData, teamData, alertsData] = await Promise.all([
                        this.fetchAPI('/api/analytics/dashboard'),
                        this.fetchAPI('/api/analytics/team-performance'),
                        this.fetchAPI('/api/analytics/alerts')
                    ]);

                    this.updateQuickStats(dashboardData);
                    this.updateTeamHeatmap(teamData);
                    this.updateRiskAlerts(alertsData);
                    this.updateDepartmentChart();
                    this.updateRadarChart(dashboardData);
                    this.loadTrendData();
                    
                } catch (error) {
                    console.error('Failed to load dashboard data:', error);
                    this.showError('Failed to load analytics data');
                } finally {
                    this.showLoading(false);
                }
            }

            async fetchAPI(endpoint) {
                const response = await fetch(endpoint, {
                    headers: {
                        'Authorization': `Bearer ${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}`,
                        'Accept': 'application/json',
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                return data.data;
            }

            updateQuickStats(data) {
                const successRate = data.success_rate || 0;
                document.getElementById('successRate').textContent = successRate;
                document.getElementById('totalObjectives').textContent = data.total_objectives || 0;
                document.getElementById('dueToday').textContent = data.objectives_due_today || 0;
                document.getElementById('activeUsers').textContent = data.active_users || 0;

                // Update trend indicator
                const trendElement = document.getElementById('successRateTrend');
                const trend = data.success_rate_trend || 0;
                if (trend > 0) {
                    trendElement.innerHTML = `<span class="text-green-500">↗ +${trend.toFixed(1)}%</span>`;
                } else if (trend < 0) {
                    trendElement.innerHTML = `<span class="text-red-500">↘ ${trend.toFixed(1)}%</span>`;
                } else {
                    trendElement.innerHTML = `<span class="text-gray-500">→ 0%</span>`;
                }

                // Update gauge chart
                this.updateGaugeChart(successRate);
                
                // Update progress overview numbers
                this.updateProgressNumbers(data);
            }

            updateGaugeChart(successRate) {
                if (!this.gaugeChart) return;
                
                this.gaugeChart.data.datasets[0].data = [successRate, 100 - successRate];
                this.gaugeChart.update();
                
                // Update gauge display value
                document.getElementById('gaugeValue').textContent = successRate.toFixed(1);
            }

            updateProgressNumbers(data) {
                // These would be populated from actual API data
                document.getElementById('completedCount').textContent = data.completed_objectives || 0;
                document.getElementById('inProgressCount').textContent = data.in_progress_objectives || 0;
                document.getElementById('notStartedCount').textContent = data.not_started_objectives || 0;
                
                // Update progress chart
                if (this.progressChart) {
                    this.progressChart.data.datasets[0].data = [
                        data.completed_objectives || 0,
                        data.in_progress_objectives || 0, 
                        data.not_started_objectives || 0
                    ];
                    this.progressChart.update();
                }
            }

            async updateDepartmentChart(metric = 'success_rate') {
                try {
                    const data = await this.fetchAPI('/api/analytics/success-rates');
                    const departments = data.departments || [];
                    
                    const labels = departments.map(dept => dept.department);
                    let values = [];
                    let chartLabel = '';
                    
                    switch(metric) {
                        case 'success_rate':
                            values = departments.map(dept => dept.success_rate);
                            chartLabel = 'Success Rate %';
                            break;
                        case 'total_objectives':
                            values = departments.map(dept => dept.total_objectives);
                            chartLabel = 'Total Objectives';
                            break;
                        case 'avg_completion_time':
                            values = departments.map(dept => dept.avg_completion_time || 0);
                            chartLabel = 'Avg Completion Time (days)';
                            break;
                    }
                    
                    if (this.departmentChart) {
                        this.departmentChart.data.labels = labels;
                        this.departmentChart.data.datasets[0].data = values;
                        this.departmentChart.data.datasets[0].label = chartLabel;
                        this.departmentChart.update();
                    }
                } catch (error) {
                    console.error('Failed to update department chart:', error);
                }
            }

            updateRadarChart(data) {
                if (!this.radarChart) return;
                
                // Mock data for demonstration - in real implementation, 
                // this would come from category-based analytics
                const currentQuarterData = [85, 92, 78, 88, 95, 82];
                const previousQuarterData = [80, 85, 75, 85, 90, 78];
                
                this.radarChart.data.datasets[0].data = currentQuarterData;
                this.radarChart.data.datasets[1].data = previousQuarterData;
                this.radarChart.update();
            }

            updateTeamHeatmap(data) {
                const container = document.getElementById('teamHeatmap');
                container.innerHTML = '';

                Object.entries(data).forEach(([department, users]) => {
                    const deptDiv = document.createElement('div');
                    deptDiv.className = 'mb-4';
                    
                    const avgSuccessRate = users.reduce((sum, user) => sum + user.success_rate, 0) / users.length;
                    const colorClass = this.getPerformanceColorClass(avgSuccessRate);
                    
                    deptDiv.innerHTML = `
                        <div class="mb-2">
                            <h4 class="font-medium text-gray-900">${department}</h4>
                            <div class="text-sm text-gray-500">${users.length} members • ${avgSuccessRate.toFixed(1)}% avg success rate</div>
                        </div>
                        <div class="grid grid-cols-5 gap-1">
                            ${users.slice(0, 10).map(user => `
                                <div class="w-8 h-8 ${colorClass} rounded flex items-center justify-center text-xs font-medium text-white" 
                                     title="${user.name}: ${user.success_rate}%">
                                    ${user.name.substring(0, 2).toUpperCase()}
                                </div>
                            `).join('')}
                        </div>
                    `;
                    
                    container.appendChild(deptDiv);
                });
            }

            updateRiskAlerts(data) {
                const container = document.getElementById('riskAlerts');
                container.innerHTML = '';

                if (data.alerts && data.alerts.length > 0) {
                    data.alerts.slice(0, 5).forEach(alert => {
                        const alertDiv = document.createElement('div');
                        alertDiv.className = `p-3 rounded-lg ${alert.type === 'overdue' ? 'bg-red-50 border border-red-200' : 'bg-yellow-50 border border-yellow-200'}`;
                        
                        alertDiv.innerHTML = `
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 ${alert.type === 'overdue' ? 'text-red-500' : 'text-yellow-500'}" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium ${alert.type === 'overdue' ? 'text-red-800' : 'text-yellow-800'}">${alert.message}</p>
                                    <p class="text-xs ${alert.type === 'overdue' ? 'text-red-600' : 'text-yellow-600'} mt-1">
                                        Completion probability: ${alert.probability}%
                                    </p>
                                </div>
                            </div>
                        `;
                        
                        container.appendChild(alertDiv);
                    });
                } else {
                    container.innerHTML = '<p class="text-gray-500 text-sm">No alerts at this time. Great job!</p>';
                }
            }

            async loadTrendData(period = 30) {
                try {
                    const data = await this.fetchAPI(`/api/analytics/trends?metric_type=success_rate&period=${period}`);
                    this.updateTrendChart(data);
                } catch (error) {
                    console.error('Failed to load trend data:', error);
                }
            }

            setupCharts() {
                // Progress Chart (Donut)
                const progressCtx = document.getElementById('progressChart').getContext('2d');
                this.progressChart = new Chart(progressCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Completed', 'In Progress', 'Not Started'],
                        datasets: [{
                            data: [0, 0, 0],
                            backgroundColor: ['#10B981', '#3B82F6', '#6B7280'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });

                // Success Rate Gauge Chart
                this.setupGaugeChart();

                // Department Bar Chart
                this.setupDepartmentChart();

                // Radar Chart
                this.setupRadarChart();

                // Trend Chart (Line)
                const trendCtx = document.getElementById('trendChart').getContext('2d');
                this.trendChart = new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: [],
                        datasets: [{
                            label: 'Success Rate %',
                            data: [],
                            borderColor: '#4F46E5',
                            backgroundColor: 'rgba(79, 70, 229, 0.1)',
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                ticks: {
                                    callback: function(value) {
                                        return value + '%';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            setupGaugeChart() {
                const gaugeCtx = document.getElementById('successGauge').getContext('2d');
                this.gaugeChart = new Chart(gaugeCtx, {
                    type: 'doughnut',
                    data: {
                        datasets: [{
                            data: [0, 100],
                            backgroundColor: ['#4F46E5', '#E5E7EB'],
                            borderWidth: 0,
                            circumference: 180,
                            rotation: 270
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                enabled: false
                            }
                        }
                    }
                });
            }

            setupDepartmentChart() {
                const deptCtx = document.getElementById('departmentChart').getContext('2d');
                this.departmentChart = new Chart(deptCtx, {
                    type: 'bar',
                    data: {
                        labels: [],
                        datasets: [{
                            label: 'Success Rate %',
                            data: [],
                            backgroundColor: [
                                '#4F46E5', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6',
                                '#06B6D4', '#84CC16', '#F97316', '#EC4899', '#6366F1'
                            ],
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                ticks: {
                                    callback: function(value) {
                                        return value + '%';
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });

                // Add event listener for metric selector
                document.getElementById('departmentMetric').addEventListener('change', (e) => {
                    this.updateDepartmentChart(e.target.value);
                });
            }

            setupRadarChart() {
                const radarCtx = document.getElementById('radarChart').getContext('2d');
                this.radarChart = new Chart(radarCtx, {
                    type: 'radar',
                    data: {
                        labels: ['Strategy', 'Operations', 'Marketing', 'Sales', 'Engineering', 'HR'],
                        datasets: [{
                            label: 'Current Quarter',
                            data: [0, 0, 0, 0, 0, 0],
                            fill: true,
                            backgroundColor: 'rgba(79, 70, 229, 0.2)',
                            borderColor: '#4F46E5',
                            pointBackgroundColor: '#4F46E5',
                            pointBorderColor: '#fff',
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: '#4F46E5'
                        }, {
                            label: 'Previous Quarter',
                            data: [0, 0, 0, 0, 0, 0],
                            fill: true,
                            backgroundColor: 'rgba(16, 185, 129, 0.2)',
                            borderColor: '#10B981',
                            pointBackgroundColor: '#10B981',
                            pointBorderColor: '#fff',
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: '#10B981'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        elements: {
                            line: {
                                borderWidth: 3
                            }
                        },
                        scales: {
                            r: {
                                angleLines: {
                                    display: false
                                },
                                suggestedMin: 0,
                                suggestedMax: 100
                            }
                        }
                    }
                });
            }

            updateTrendChart(data) {
                if (!this.trendChart || !data.company) return;

                const companyData = data.company;
                const labels = companyData.map(item => new Date(item.date).toLocaleDateString());
                const values = companyData.map(item => item.value);

                this.trendChart.data.labels = labels;
                this.trendChart.data.datasets[0].data = values;
                this.trendChart.update();
            }

            getPerformanceColorClass(successRate) {
                if (successRate >= 90) return 'bg-green-500';
                if (successRate >= 70) return 'bg-yellow-500';
                return 'bg-red-500';
            }

            showLoading(show) {
                const loadingElement = document.getElementById('loadingIndicator');
                const quickStats = document.getElementById('quickStats');
                
                if (show) {
                    loadingElement.classList.remove('hidden');
                    quickStats.classList.add('opacity-50');
                } else {
                    loadingElement.classList.add('hidden');
                    quickStats.classList.remove('opacity-50');
                }
            }

            showError(message) {
                // You can implement a toast notification system here
                console.error(message);
            }

            async exportData(format = 'json') {
                try {
                    this.showExportLoading(true);
                    
                    if (format === 'pdf' || format === 'csv') {
                        // For PDF and CSV, we need to make a direct download request
                        const url = `/api/analytics/export?type=success_rates&format=${format}`;
                        
                        // Create a temporary form for file download
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = url;
                        
                        // Add CSRF token
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        form.appendChild(csrfInput);
                        
                        // Add format input
                        const formatInput = document.createElement('input');
                        formatInput.type = 'hidden';
                        formatInput.name = 'format';
                        formatInput.value = format;
                        form.appendChild(formatInput);
                        
                        // Add type input
                        const typeInput = document.createElement('input');
                        typeInput.type = 'hidden';
                        typeInput.name = 'type';
                        typeInput.value = 'success_rates';
                        form.appendChild(typeInput);
                        
                        document.body.appendChild(form);
                        form.submit();
                        document.body.removeChild(form);
                        
                        this.showExportSuccess(format);
                    } else {
                        // JSON export
                        const data = await this.fetchAPI('/api/analytics/export?type=dashboard&format=json');
                        
                        const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
                        const url = URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = `analytics_dashboard_${new Date().toISOString().split('T')[0]}.json`;
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        URL.revokeObjectURL(url);
                        
                        this.showExportSuccess('JSON');
                    }
                } catch (error) {
                    console.error('Export failed:', error);
                    this.showError(`Failed to export ${format.toUpperCase()} data`);
                } finally {
                    this.showExportLoading(false);
                }
            }

            showExportLoading(show) {
                // You could add a loading indicator here
                if (show) {
                    console.log('Export in progress...');
                } else {
                    console.log('Export completed.');
                }
            }

            showExportSuccess(format) {
                // You could show a success toast here
                console.log(`${format} export completed successfully!`);
            }
        }

        // Initialize dashboard when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            new AnalyticsDashboard();
        });
    </script>
</x-app-layout> 