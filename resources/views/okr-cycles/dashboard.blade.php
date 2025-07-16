<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    🎯 {{ __('OKR Dashboard') }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Track objectives, measure progress, and drive organizational success
                </p>
            </div>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('okr-cycles.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    🗓️ Manage Cycles
                </a>
                <a href="{{ route('okr-check-ins.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    ✅ Quick Check-in
                </a>
            </div>
        </div>
    </x-slot>

    <div class="{{ $currentCycle ? 'py-6' : 'py-2' }}">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Error Message -->
            @if(session('error'))
                <div class="mb-4 bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Dashboard Error</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <p>{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if($currentCycle)
            <!-- Current Cycle Overview -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-lg p-8 mb-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Cycle Info -->
                <div class="lg:col-span-2">
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="text-4xl">🎯</div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">{{ $currentCycle->name }}</h2>
                            <p class="text-gray-600">{{ $currentCycle->description ?? 'Current OKR Cycle' }}</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-white rounded-lg p-4">
                            <div class="text-2xl font-bold text-blue-600">{{ $currentCycle->getDaysRemaining() }}</div>
                            <div class="text-sm text-gray-600">Days Remaining</div>
                        </div>
                        <div class="bg-white rounded-lg p-4">
                            <div class="text-2xl font-bold text-green-600">{{ number_format($currentCycle->getProgressPercentage(), 1) }}%</div>
                            <div class="text-sm text-gray-600">Time Progress</div>
                        </div>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div>
                        <div class="flex justify-between text-sm text-gray-700 mb-2">
                            <span>{{ $currentCycle->start_date->format('M j') }}</span>
                            <span>{{ number_format($currentCycle->getProgressPercentage(), 1) }}% Complete</span>
                            <span>{{ $currentCycle->end_date->format('M j, Y') }}</span>
                        </div>
                        <div class="bg-white rounded-full h-3">
                            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-3 rounded-full" style="width: {{ $currentCycle->getProgressPercentage() }}%"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Key Milestones -->
                <div class="bg-white rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Key Dates</h3>
                    <div class="space-y-3">
                        @if($currentCycle->mid_quarter_review)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Mid-Quarter Review</span>
                            <span class="text-sm font-medium">{{ $currentCycle->mid_quarter_review->format('M j') }}</span>
                        </div>
                        @endif
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Cycle End</span>
                            <span class="text-sm font-medium">{{ $currentCycle->end_date->format('M j') }}</span>
                        </div>
                        @if($currentCycle->scoring_deadline)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Scoring Due</span>
                            <span class="text-sm font-medium">{{ $currentCycle->scoring_deadline->format('M j') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @else
            <!-- No Current Cycle -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 {{ session('error') ? 'mt-0' : 'mt-0' }} mb-6">
                <div class="flex items-center">
                    <div class="text-4xl mr-4">⚠️</div>
                    <div>
                        <h3 class="text-lg font-semibold text-yellow-800">No Active OKR Cycle</h3>
                        <p class="text-yellow-700 mt-1">Initialize OKR cycles to get started with tracking objectives and key results.</p>
                        <div class="mt-4">
                            <a href="{{ route('okr-cycles.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700">
                                🚀 Initialize Cycles
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

            <!-- Health Metrics -->
            @if($currentCycle && ($healthReport['total_objectives'] ?? 0) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 {{ $currentCycle ? 'mb-8' : 'mb-6' }}">
            <!-- Total Objectives -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="text-3xl mr-4">📋</div>
                    <div>
                        <div class="text-2xl font-bold text-gray-900">{{ $healthReport['total_objectives'] }}</div>
                        <div class="text-sm text-gray-600">Total Objectives</div>
                    </div>
                </div>
            </div>

            <!-- Success Rate -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="text-3xl mr-4">🎯</div>
                    <div>
                        <div class="text-2xl font-bold text-green-600">{{ $healthReport['success_rate'] }}%</div>
                        <div class="text-sm text-gray-600">Success Rate</div>
                    </div>
                </div>
            </div>

            <!-- Average Score -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="text-3xl mr-4">📊</div>
                    <div>
                        <div class="text-2xl font-bold text-blue-600">{{ $healthReport['average_score'] }}</div>
                        <div class="text-sm text-gray-600">Avg OKR Score</div>
                    </div>
                </div>
            </div>

            <!-- Confidence Level -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="text-3xl mr-4">💪</div>
                    <div>
                        <div class="text-2xl font-bold text-purple-600">{{ number_format((float)($healthReport['average_confidence'] ?? 0.5) * 100, 0) }}%</div>
                        <div class="text-sm text-gray-600">Avg Confidence</div>
                    </div>
                </div>
            </div>
        </div>
        @elseif(!$currentCycle)
            <!-- Show minimal health metrics only when no cycle -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-4">
                <!-- Total Objectives -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="text-3xl mr-4">📋</div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{{ $healthReport['total_objectives'] }}</div>
                            <div class="text-sm text-gray-600">Total Objectives</div>
                        </div>
                    </div>
                </div>

                <!-- Success Rate -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="text-3xl mr-4">🎯</div>
                        <div>
                            <div class="text-2xl font-bold text-green-600">{{ $healthReport['success_rate'] }}%</div>
                            <div class="text-sm text-gray-600">Success Rate</div>
                        </div>
                    </div>
                </div>

                <!-- Average Score -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="text-3xl mr-4">📊</div>
                        <div>
                            <div class="text-2xl font-bold text-blue-600">{{ $healthReport['average_score'] }}</div>
                            <div class="text-sm text-gray-600">Avg OKR Score</div>
                        </div>
                    </div>
                </div>

                <!-- Confidence Level -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="text-3xl mr-4">💪</div>
                        <div>
                            <div class="text-2xl font-bold text-purple-600">{{ number_format((float)($healthReport['average_confidence'] ?? 0.5) * 100, 0) }}%</div>
                            <div class="text-sm text-gray-600">Avg Confidence</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        <!-- Quick Actions & Recent Activity -->
        @if($currentCycle && (($healthReport['total_objectives'] ?? 0) > 0 || $recentCheckIns->isNotEmpty()))
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('objectives.create') }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                        📝 Create Objective
                    </a>
                    <a href="{{ route('okr-check-ins.create') }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        ✅ Record Check-in
                    </a>
                    <a href="{{ route('objectives.index') }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        👀 View My Objectives
                    </a>
                </div>
            </div>

            <!-- Recent Check-ins -->
            @if($recentCheckIns->isNotEmpty())
            <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Check-ins</h3>
                    <a href="{{ route('okr-check-ins.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View All →
                    </a>
                </div>
                
                @foreach($recentCheckIns as $checkIn)
                <div class="flex items-center space-x-4 py-3 border-b border-gray-100 last:border-b-0">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-{{ $checkIn->getConfidenceStatus() === 'high' ? 'green' : ($checkIn->getConfidenceStatus() === 'medium' ? 'yellow' : 'red') }}-100 rounded-full flex items-center justify-center">
                            <span class="text-{{ $checkIn->getConfidenceStatus() === 'high' ? 'green' : ($checkIn->getConfidenceStatus() === 'medium' ? 'yellow' : 'red') }}-600 text-sm font-medium">
                                {{ number_format((float)($checkIn->confidence_level ?? 0.5) * 100, 0) }}%
                            </span>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">
                            {{ $checkIn->objective?->title ?? $checkIn->keyResult?->title }}
                        </p>
                        <p class="text-sm text-gray-500 truncate">
                            {{ $checkIn->progress_notes }}
                        </p>
                    </div>
                    <div class="flex-shrink-0 text-right">
                        <p class="text-xs text-gray-400">{{ $checkIn->check_in_date->format('M j') }}</p>
                        <p class="text-xs font-medium text-gray-600">{{ ucfirst($checkIn->check_in_type) }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @endif

        <!-- Distribution Charts -->
        @if($currentCycle && ($healthReport['total_objectives'] ?? 0) > 0)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- OKR Type Distribution -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">OKR Type Distribution</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 bg-blue-500 rounded"></div>
                            <span class="text-sm text-gray-700">Committed</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-medium">{{ $healthReport['distribution']['committed'] }}</span>
                            <div class="w-20 bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ (int)($healthReport['total_objectives'] ?? 0) > 0 ? ((int)($healthReport['distribution']['committed'] ?? 0) / (int)($healthReport['total_objectives'] ?? 1) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 bg-purple-500 rounded"></div>
                            <span class="text-sm text-gray-700">Aspirational</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-medium">{{ $healthReport['distribution']['aspirational'] }}</span>
                            <div class="w-20 bg-gray-200 rounded-full h-2">
                                <div class="bg-purple-500 h-2 rounded-full" style="width: {{ (int)($healthReport['total_objectives'] ?? 0) > 0 ? ((int)($healthReport['distribution']['aspirational'] ?? 0) / (int)($healthReport['total_objectives'] ?? 1) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Level Distribution -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Level Distribution</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 bg-green-500 rounded"></div>
                            <span class="text-sm text-gray-700">Company</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-medium">{{ $healthReport['levels']['company'] }}</span>
                            <div class="w-20 bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: {{ (int)($healthReport['total_objectives'] ?? 0) > 0 ? ((int)($healthReport['levels']['company'] ?? 0) / (int)($healthReport['total_objectives'] ?? 1) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 bg-yellow-500 rounded"></div>
                            <span class="text-sm text-gray-700">Team</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-medium">{{ $healthReport['levels']['team'] }}</span>
                            <div class="w-20 bg-gray-200 rounded-full h-2">
                                <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ (int)($healthReport['total_objectives'] ?? 0) > 0 ? ((int)($healthReport['levels']['team'] ?? 0) / (int)($healthReport['total_objectives'] ?? 1) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 bg-indigo-500 rounded"></div>
                            <span class="text-sm text-gray-700">Individual</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-medium">{{ $healthReport['levels']['individual'] }}</span>
                            <div class="w-20 bg-gray-200 rounded-full h-2">
                                <div class="bg-indigo-500 h-2 rounded-full" style="width: {{ (int)($healthReport['total_objectives'] ?? 0) > 0 ? ((int)($healthReport['levels']['individual'] ?? 0) / (int)($healthReport['total_objectives'] ?? 1) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($healthReport['needs_attention'] > 0)
        <!-- Attention Required -->
        <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-lg p-6">
            <div class="flex items-center">
                <div class="text-2xl mr-3">⚠️</div>
                <div>
                    <h3 class="text-lg font-semibold text-yellow-800">Attention Required</h3>
                    <p class="text-yellow-700">
                        {{ $healthReport['needs_attention'] }} objective(s) need attention due to low confidence or poor progress.
                    </p>
                </div>
                <div class="ml-auto">
                    <a href="{{ route('objectives.index') }}?filter=needs_attention" class="inline-flex items-center px-4 py-2 border border-yellow-300 rounded-md shadow-sm text-sm font-medium text-yellow-700 bg-yellow-100 hover:bg-yellow-200">
                        Review Objectives
                    </a>
                </div>
            </div>
        </div>
        @endif
        </div>
    </div>
</x-app-layout> 