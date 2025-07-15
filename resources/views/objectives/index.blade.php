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
                {{ __('Objectives') }}
            </h2>
            <a href="{{ route('objectives.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Objective
            </a>
        </div>
    </x-slot>

    <!-- Add Sortable.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div id="objectives-container" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @forelse($objectives as $objective)
                    <div class="objective-card bg-white overflow-hidden shadow-sm rounded-lg hover:shadow-md transition-shadow duration-200" data-id="{{ $objective->id }}">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <span class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-600 font-semibold text-lg">
                                        {{ $loop->iteration }}
                                    </span>
                                    <h3 class="ml-3 text-xl font-semibold text-gray-900 truncate">
                                        <a href="{{ route('objectives.show', $objective) }}" class="hover:text-blue-600 transition-colors duration-150">
                                            {{ $objective->title }}
                                        </a>
                                    </h3>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $objective->progress >= 100 ? 'bg-green-100 text-green-800' : ($objective->progress >= 50 ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ $objective->progress }}%
                                </span>
                            </div>

                            <!-- Description with Read More -->
                            <div class="mb-4">
                                @php
                                    $truncatedDesc = truncateDescription($objective->description, 120);
                                    $needsReadMore = Str::length($objective->description) > 120;
                                @endphp
                                <p class="text-gray-600 text-sm">{{ $truncatedDesc }}</p>
                                @if($needsReadMore)
                                    <a href="{{ route('objectives.show', $objective) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors duration-200">
                                        Read more →
                                    </a>
                                @endif
                            </div>

                            <div class="mb-4">
                                <div class="flex items-center justify-between text-sm text-gray-600 mb-1">
                                    <span>Overall Progress</span>
                                    <span class="font-medium">{{ $objective->progress }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="h-2.5 rounded-full transition-all duration-300 ease-in-out {{ $objective->progress >= 100 ? 'bg-green-500' : ($objective->progress >= 50 ? 'bg-blue-500' : 'bg-blue-500') }}" 
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

                            <div class="space-y-3">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Time Period</span>
                                    <span class="font-medium text-gray-900">{{ ucfirst($objective->time_period) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Due Date</span>
                                    <span class="font-medium {{ getDueDateColor($objective->end_date) }}">
                                        {{ $objective->end_date->format('M d, Y') }}
                                    </span>
                                </div>
                            </div>

                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <a href="{{ route('objectives.show', $objective) }}" 
                                   class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors duration-150">
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
                        <div class="text-center py-12 bg-white rounded-lg shadow-sm">
                            <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Objectives Yet</h3>
                            <p class="text-gray-600 mb-6">Get started by creating your first objective.</p>
                            <a href="{{ route('objectives.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                                Create Objective
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <style>
        /* Drag and drop styles */
        .sortable-ghost {
            opacity: 0.4;
        }
        .sortable-chosen {
            cursor: grabbing;
        }
        .objective-card {
            cursor: grab;
        }
        .objective-card:hover {
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
                        // Save the new order to localStorage
                        const objectiveIds = Array.from(objectivesContainer.children).map(el => el.dataset.id);
                        localStorage.setItem('objectives_page_order', JSON.stringify(objectiveIds));
                        
                        // Show success message
                        showNotification('Objectives reordered successfully!', 'success');
                    }
                });

                // Restore order from localStorage
                const savedOrder = localStorage.getItem('objectives_page_order');
                if (savedOrder) {
                    try {
                        const order = JSON.parse(savedOrder);
                        restoreOrder(objectivesContainer, order);
                    } catch (e) {
                        console.warn('Could not restore objectives order:', e);
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