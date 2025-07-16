@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="sm:flex sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">OKR Cycles</h1>
                    <p class="mt-2 text-sm text-gray-700">
                        Manage quarterly OKR cycles and track organizational progress
                    </p>
                </div>
                <div class="mt-4 sm:mt-0 sm:flex sm:space-x-3">
                    <a href="{{ route('okr.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        📊 OKR Dashboard
                    </a>
                    <a href="{{ route('okr-cycles.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                        ➕ Create Cycle
                    </a>
                </div>
            </div>
        </div>

        <!-- Current & Active Cycle Status -->
        @if($currentCycle || $activeCycle)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            @if($currentCycle)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-blue-900">Current Cycle</h3>
                        <p class="text-blue-700 font-bold">{{ $currentCycle->name }}</p>
                        <p class="text-sm text-blue-600 mt-1">{{ $currentCycle->getDaysRemaining() }} days remaining</p>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-blue-900">{{ number_format($currentCycle->getProgressPercentage(), 1) }}%</div>
                        <div class="text-sm text-blue-600">Progress</div>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="bg-blue-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $currentCycle->getProgressPercentage() }}%"></div>
                    </div>
                </div>
            </div>
            @endif

            @if($activeCycle && $activeCycle->id !== $currentCycle?->id)
            <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-green-900">Active Cycle</h3>
                        <p class="text-green-700 font-bold">{{ $activeCycle->name }}</p>
                        <p class="text-sm text-green-600 mt-1">Status: {{ ucfirst($activeCycle->status) }}</p>
                    </div>
                    <div class="text-2xl">🎯</div>
                </div>
            </div>
            @endif
        </div>
        @endif

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow mb-6 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Initialize Year -->
                <form action="{{ route('okr-cycles.initialize-year') }}" method="POST" class="inline">
                    @csrf
                    <input type="number" name="year" value="{{ date('Y') }}" min="2020" max="2030" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm mb-2" placeholder="Year">
                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                        🗓️ Initialize Year
                    </button>
                </form>
                
                <!-- Go to Check-ins -->
                <a href="{{ route('okr-check-ins.index') }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    📝 View Check-ins
                </a>
                
                <!-- Create Check-in -->
                <a href="{{ route('okr-check-ins.create') }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700">
                    ✅ Quick Check-in
                </a>
            </div>
        </div>

        <!-- Cycles Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($cycles as $cycle)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                <!-- Cycle Header -->
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-gray-900">{{ $cycle->name }}</h3>
                        <div class="flex items-center space-x-2">
                            <!-- Status Badge -->
                            @php
                                $statusColors = [
                                    'planning' => 'bg-yellow-100 text-yellow-800',
                                    'active' => 'bg-green-100 text-green-800',
                                    'review' => 'bg-blue-100 text-blue-800',
                                    'closed' => 'bg-gray-100 text-gray-800'
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$cycle->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($cycle->status) }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Cycle Dates -->
                    <div class="mt-3 text-sm text-gray-600">
                        <div class="flex items-center space-x-4">
                            <span>📅 {{ $cycle->start_date->format('M j') }} - {{ $cycle->end_date->format('M j, Y') }}</span>
                            @if($cycle->isCurrent())
                                <span class="text-blue-600 font-medium">🔄 Current</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Cycle Stats -->
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Objectives</span>
                        <span class="font-medium">{{ $cycle->objectives->count() }}</span>
                    </div>
                    <div class="mt-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Progress</span>
                            <span class="font-medium">{{ number_format($cycle->getProgressPercentage(), 1) }}%</span>
                        </div>
                        <div class="mt-1 bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $cycle->getProgressPercentage() }}%"></div>
                        </div>
                    </div>
                    
                    @if($cycle->getDaysRemaining() > 0)
                    <div class="mt-3 text-sm">
                        <span class="text-gray-600">{{ $cycle->getDaysRemaining() }} days remaining</span>
                    </div>
                    @endif
                </div>

                <!-- Cycle Actions -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <a href="{{ route('okr-cycles.show', $cycle) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            View Details →
                        </a>
                        
                        <div class="flex items-center space-x-2">
                            @if($cycle->status === 'planning')
                                <form action="{{ route('okr-cycles.start', $cycle) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700" 
                                            onclick="return confirm('Start this cycle? This will close any currently active cycle.')">
                                        ▶️ Start
                                    </button>
                                </form>
                            @elseif($cycle->status === 'active')
                                <form action="{{ route('okr-cycles.close', $cycle) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-white bg-red-600 hover:bg-red-700"
                                            onclick="return confirm('Close this cycle? Final scores will be calculated.')">
                                        ⏹️ Close
                                    </button>
                                </form>
                            @endif
                            
                            <a href="{{ route('okr-cycles.edit', $cycle) }}" class="inline-flex items-center px-3 py-1 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                ✏️ Edit
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full">
                <div class="text-center py-12">
                    <div class="text-6xl mb-4">🎯</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No OKR Cycles</h3>
                    <p class="text-gray-600 mb-6">Get started by creating your first OKR cycle or initializing a full year.</p>
                    <div class="space-y-3 sm:space-y-0 sm:space-x-3 sm:flex sm:justify-center">
                        <a href="{{ route('okr-cycles.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                            Create First Cycle
                        </a>
                    </div>
                </div>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($cycles->hasPages())
        <div class="mt-8">
            {{ $cycles->links() }}
        </div>
        @endif
    </div>
</div>
@endsection 