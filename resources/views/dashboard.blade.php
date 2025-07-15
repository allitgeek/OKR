@php
    use Illuminate\Support\Str;
    
    // Helper function to get due date color based on time remaining
    function getDueDateColor($endDate) {
        if (!$endDate) return 'text-gray-600';
        
        $now = now();
        $totalDuration = $endDate->diffInHours($endDate->copy()->subMonths(1)); // Assume 1 month period as default
        $remainingHours = $now->diffInHours($endDate, false);
        
        // If overdue
        if ($remainingHours < 0) {
            return 'text-red-600 font-semibold';
        }
        
        // If 48 hours or less remaining
        if ($remainingHours <= 48) {
            return 'text-red-500 font-medium';
        }
        
        // If 50% or more time has passed
        $timePassedPercentage = (($totalDuration - $remainingHours) / $totalDuration) * 100;
        if ($timePassedPercentage >= 50) {
            return 'text-orange-500 font-medium';
        }
        
        // Default blue
        return 'text-blue-600';
    }
    
    // Helper function to truncate description
    function truncateDescription($description, $length = 150) {
        return Str::length($description) > $length ? Str::limit($description, $length, '...') : $description;
    }
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            @can('manage-users')
            <div class="flex space-x-3">
                <button x-data @click="$dispatch('open-modal', 'create-objective')" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Objective
                </button>
                <button x-data @click="$dispatch('open-modal', 'create-task')" class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2.5 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Task
                </button>
            </div>
            @endcan
        </div>
    </x-slot>

    <!-- Add Sortable.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div x-data="{ show: true }" 
                     x-show="show" 
                     x-init="setTimeout(() => show = false, 4000)"
                     class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg relative mb-6 shadow-sm" 
                     role="alert">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>{{ session('success') }}</span>
                        <button @click="show = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                            <svg class="fill-current h-4 w-4 text-green-600" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div x-data="{ show: true }" 
                     x-show="show" 
                     x-init="setTimeout(() => show = false, 4000)"
                     class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg relative mb-6 shadow-sm" 
                     role="alert">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <span>{{ session('error') }}</span>
                        <button @click="show = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                            <svg class="fill-current h-4 w-4 text-red-600" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            <!-- Dashboard Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Objectives</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $objectives->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Completed</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $objectives->where('progress', '>=', 100)->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Active Tasks</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $tasks->where('status', '!=', 'completed')->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Team Members</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ isset($users) ? $users->count() : 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Objectives Section -->
            <div class="mb-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Objectives</h3>
                    <a href="{{ route('objectives.index') }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors duration-200">
                        View All
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>

                <div id="objectives-container" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @forelse ($objectives->take(6) as $objective)
                        <div class="objective-card bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg transition-shadow duration-300" data-id="{{ $objective->id }}">
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <h4 class="text-lg font-semibold text-gray-900 line-clamp-1">
                                        <a href="{{ route('objectives.show', $objective) }}" class="hover:text-blue-600 transition-colors duration-200">
                                            {{ $objective->title }}
                                        </a>
                                    </h4>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $objective->progress >= 100 ? 'bg-green-100 text-green-800' : ($objective->progress >= 50 ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ number_format($objective->progress, 0) }}%
                                    </span>
                                </div>

                                <!-- Description with Read More -->
                                <div class="mb-4">
                                    @php
                                        $truncatedDesc = truncateDescription($objective->description, 150);
                                        $needsReadMore = Str::length($objective->description) > 150;
                                    @endphp
                                    <p class="text-gray-600 text-sm">{{ $truncatedDesc }}</p>
                                    @if($needsReadMore)
                                        <a href="{{ route('objectives.show', $objective) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors duration-200">
                                            Read more →
                                        </a>
                                    @endif
                                </div>

                                                <!-- Progress Bar -->
                <div class="mb-4">
                    <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
                        <span>Progress</span>
                        <span class="font-medium">{{ number_format($objective->progress, 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="h-2.5 rounded-full transition-all duration-300 ease-in-out {{ $objective->progress >= 100 ? 'bg-green-500' : 'bg-blue-500' }}" 
                             style="width: {{ $objective->progress }}%">
                        </div>
                    </div>
                </div>

                <!-- KR Statistics -->
                <div class="mb-4 grid grid-cols-3 gap-2 text-xs text-gray-600">
                    <div class="bg-gray-50 rounded-lg p-2.5 text-center">
                        <div class="font-semibold text-gray-900">{{ $objective->keyResults->count() }}</div>
                        <div>Total KR</div>
                    </div>
                    <div class="bg-green-50 rounded-lg p-2.5 text-center">
                        <div class="font-semibold text-green-700">{{ $objective->keyResults->where('progress', '>=', 100)->count() }}</div>
                        <div class="text-green-600">Completed KR</div>
                    </div>
                    <div class="bg-yellow-50 rounded-lg p-2.5 text-center">
                        <div class="font-semibold text-yellow-700">{{ $objective->keyResults->where('progress', '<', 100)->count() }}</div>
                        <div class="text-yellow-600">Pending KR</div>
                    </div>
                </div>

                                <!-- Metadata -->
                                <div class="space-y-2 text-sm text-gray-600">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <span>{{ $objective->user->name }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="font-medium {{ getDueDateColor($objective->end_date) }}">
                                            {{ $objective->end_date ? $objective->end_date->format('M d, Y') : 'No due date' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Action Button -->
                                <div class="mt-4 pt-4 border-t border-gray-100">
                                    <a href="{{ route('objectives.show', $objective) }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors duration-200">
                                        View Details
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full">
                            <div class="text-center py-12 bg-white rounded-xl shadow-sm border border-gray-200">
                                <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No Objectives Yet</h3>
                                <p class="text-gray-600 mb-6">Get started by creating your first objective.</p>
                                @can('manage-users')
                                <button x-data @click="$dispatch('open-modal', 'create-objective')" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                                    Create Objective
                                </button>
                                @endcan
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Tasks Section -->
            <div>
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Tasks</h3>
                    <a href="{{ route('tasks.index') }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors duration-200">
                        View All
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>

                <div id="tasks-container" class="grid gap-4 md:grid-cols-2">
                    @forelse ($tasks->take(4) as $task)
                        <div class="task-card bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg transition-shadow duration-300 p-6" data-id="{{ $task->id }}">
                            <div class="flex justify-between items-start mb-3">
                                <h4 class="text-lg font-semibold text-gray-900 line-clamp-1">
                                    <a href="{{ route('tasks.show', $task) }}" class="hover:text-blue-600 transition-colors duration-200">
                                        {{ $task->title }}
                                    </a>
                                </h4>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium ml-2 {{ 
                                    $task->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                    ($task->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 
                                    ($task->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) 
                                }}">
                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                </span>
                            </div>

                            <p class="text-gray-600 text-sm line-clamp-2 mb-4">{{ $task->description }}</p>

                            <div class="flex items-center justify-between text-sm text-gray-600">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span>{{ $task->assignee->name }}</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="{{ getDueDateColor($task->due_date) }}">{{ $task->due_date?->format('M d') ?? 'No due date' }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full">
                            <div class="text-center py-12 bg-white rounded-xl shadow-sm border border-gray-200">
                                <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No Tasks Yet</h3>
                                <p class="text-gray-600 mb-6">Create your first task to get started.</p>
                                @can('manage-users')
                                <button x-data @click="$dispatch('open-modal', 'create-task')" class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2.5 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                                    Create Task
                                </button>
                                @endcan
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Create Objective Modal -->
    <x-modal name="create-objective" :show="false" maxWidth="lg">
        <form method="POST" action="{{ route('objectives.create.super') }}" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-gray-900 mb-6">
                {{ __('Create New Objective') }}
            </h2>

            <div class="space-y-4">
                <div>
                    <x-input-label for="title" :value="__('Title')" />
                    <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" required />
                    <x-input-error class="mt-2" :messages="$errors->get('title')" />
                </div>

                <div>
                    <x-input-label for="description" :value="__('Description')" />
                    <textarea id="description" name="description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="3" required></textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('description')" />
                </div>

                <div>
                    <x-input-label for="user_id" :value="__('Assign To')" />
                    <select id="user_id" name="user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ $user->id === auth()->id() ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('user_id')" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="start_date" :value="__('Start Date')" />
                        <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" required min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" />
                        <x-input-error class="mt-2" :messages="$errors->get('start_date')" />
                    </div>

                    <div>
                        <x-input-label for="end_date" :value="__('Due Date')" />
                        <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full" required min="{{ date('Y-m-d') }}" />
                        <x-input-error class="mt-2" :messages="$errors->get('end_date')" />
                    </div>
                </div>

                <div>
                    <x-input-label for="time_period" :value="__('Time Period')" />
                    <select id="time_period" name="time_period" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">Select Period</option>
                        <option value="monthly">Monthly</option>
                        <option value="quarterly">Quarterly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('time_period')" />
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <x-secondary-button @click="$dispatch('close-modal', 'create-objective')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button>
                    {{ __('Create Objective') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    <!-- Create Task Modal -->
    <x-modal name="create-task" :show="false" maxWidth="lg">
        <form method="POST" action="{{ route('tasks.create.super') }}" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-gray-900 mb-6">
                {{ __('Create New Task') }}
            </h2>

            <div class="space-y-4">
                <div>
                    <x-input-label for="task_title" :value="__('Title')" />
                    <x-text-input id="task_title" name="title" type="text" class="mt-1 block w-full" required />
                    <x-input-error class="mt-2" :messages="$errors->get('title')" />
                </div>

                <div>
                    <x-input-label for="task_description" :value="__('Description')" />
                    <textarea id="task_description" name="description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="3" required></textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('description')" />
                </div>

                <div>
                    <x-input-label for="task_assignee_id" :value="__('Assign To')" />
                    <select id="task_assignee_id" name="assignee_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('assignee_id')" />
                </div>

                <div>
                    <x-input-label for="task_due_date" :value="__('Due Date (Optional)')" />
                    <x-text-input id="task_due_date" name="due_date" type="date" class="mt-1 block w-full" min="{{ date('Y-m-d') }}" />
                    <x-input-error class="mt-2" :messages="$errors->get('due_date')" />
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <x-secondary-button @click="$dispatch('close-modal', 'create-task')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button>
                    {{ __('Create Task') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    <style>
        .line-clamp-1 {
            overflow: hidden;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 1;
        }
        .line-clamp-2 {
            overflow: hidden;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
        }

        /* Drag and drop styles */
        .sortable-ghost {
            opacity: 0.4;
        }
        .sortable-chosen {
            cursor: grabbing;
        }
        .objective-card,
        .task-card {
            cursor: grab;
        }
        .objective-card:hover,
        .task-card:hover {
            cursor: grab;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize sortable for objectives
            const objectivesContainer = document.getElementById('objectives-container');
            if (objectivesContainer) {
                new Sortable(objectivesContainer, {
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    onEnd: function(evt) {
                        // Save the new order to localStorage or send to server
                        const objectiveIds = Array.from(objectivesContainer.children).map(el => el.dataset.id);
                        localStorage.setItem('dashboard_objectives_order', JSON.stringify(objectiveIds));
                        
                        // Show success message
                        showNotification('Objectives reordered successfully!', 'success');
                    }
                });

                // Restore order from localStorage
                const savedObjectivesOrder = localStorage.getItem('dashboard_objectives_order');
                if (savedObjectivesOrder) {
                    try {
                        const order = JSON.parse(savedObjectivesOrder);
                        restoreOrder(objectivesContainer, order);
                    } catch (e) {
                        console.warn('Could not restore objectives order:', e);
                    }
                }
            }

            // Initialize sortable for tasks
            const tasksContainer = document.getElementById('tasks-container');
            if (tasksContainer) {
                new Sortable(tasksContainer, {
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    onEnd: function(evt) {
                        // Save the new order to localStorage
                        const taskIds = Array.from(tasksContainer.children).map(el => el.dataset.id);
                        localStorage.setItem('dashboard_tasks_order', JSON.stringify(taskIds));
                        
                        // Show success message
                        showNotification('Tasks reordered successfully!', 'success');
                    }
                });

                // Restore order from localStorage
                const savedTasksOrder = localStorage.getItem('dashboard_tasks_order');
                if (savedTasksOrder) {
                    try {
                        const order = JSON.parse(savedTasksOrder);
                        restoreOrder(tasksContainer, order);
                    } catch (e) {
                        console.warn('Could not restore tasks order:', e);
                    }
                }
            }

            // Helper function to restore order
            function restoreOrder(container, order) {
                const items = Array.from(container.children);
                const orderedItems = [];
                
                // First, add items in the saved order
                order.forEach(id => {
                    const item = items.find(el => el.dataset.id === id);
                    if (item) {
                        orderedItems.push(item);
                    }
                });
                
                // Then add any items not in the saved order
                items.forEach(item => {
                    if (!orderedItems.includes(item)) {
                        orderedItems.push(item);
                    }
                });
                
                // Reorder the DOM
                orderedItems.forEach(item => {
                    container.appendChild(item);
                });
            }

            // Helper function to show notifications
            function showNotification(message, type = 'success') {
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white z-50 transform transition-all duration-300 ${
                    type === 'success' ? 'bg-green-500' : 'bg-red-500'
                }`;
                notification.textContent = message;
                
                document.body.appendChild(notification);
                
                // Animate in
                setTimeout(() => {
                    notification.style.transform = 'translateX(0)';
                }, 100);
                
                // Remove after 3 seconds
                setTimeout(() => {
                    notification.style.transform = 'translateX(100%)';
                    setTimeout(() => {
                        document.body.removeChild(notification);
                    }, 300);
                }, 3000);
            }
        });
    </script>
</x-app-layout>
