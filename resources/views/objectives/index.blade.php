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
            <!-- Filter Controls -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-sm font-medium text-gray-700 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"></path>
                        </svg>
                        Filters
                    </h4>
                    <button id="clear-filters" class="text-sm text-gray-500 hover:text-gray-700 font-medium">Clear All</button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <!-- Status Filter -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                        <select id="status-filter" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="all">All Objectives</option>
                            <option value="not-started">Not Started (0%)</option>
                            <option value="in-progress">In Progress (1-99%)</option>
                            <option value="completed">Completed (100%)</option>
                            <option value="overdue">Overdue</option>
                        </select>
                    </div>
                    
                    <!-- Time Filter -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Time</label>
                        <select id="time-filter" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="all">All Time</option>
                            <option value="latest">Latest Created</option>
                            <option value="oldest">Oldest Created</option>
                            <option value="due-soon">Due Soon (7 days)</option>
                            <option value="due-month">Due This Month</option>
                            <option value="recently-updated">Recently Updated</option>
                        </select>
                    </div>
                    
                    <!-- Progress Filter -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Progress</label>
                        <select id="progress-filter" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="all">All Progress</option>
                            <option value="0">0%</option>
                            <option value="1-25">1% - 25%</option>
                            <option value="26-50">26% - 50%</option>
                            <option value="51-75">51% - 75%</option>
                            <option value="76-99">76% - 99%</option>
                            <option value="100">100%</option>
                        </select>
                    </div>
                    
                    <!-- Cycle Filter -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">OKR Cycle</label>
                        <select id="cycle-filter" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="all">All Cycles</option>
                            @foreach($objectives->pluck('cycle_id')->unique()->filter() as $cycle)
                                <option value="{{ $cycle }}">{{ $cycle }}</option>
                            @endforeach
                            <option value="unassigned">Not Assigned</option>
                        </select>
                    </div>
                    
                    <!-- Owner Filter -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Owner</label>
                        <select id="owner-filter" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="all">All Owners</option>
                            <option value="mine">My Objectives</option>
                            <option value="others">Others</option>
                            @foreach($objectives->pluck('user')->unique() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <!-- Filter Results Info -->
                <div id="filter-results" class="mt-3 text-sm text-gray-600 flex items-center justify-between">
                    <span id="results-count">Showing all {{ $objectives->count() }} objectives</span>
                    <div class="flex space-x-2">
                        <button id="sort-alpha" class="px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded transition-colors" title="Sort A-Z">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path>
                            </svg>
                        </button>
                        <button id="sort-progress" class="px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded transition-colors" title="Sort by Progress">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div id="objectives-container" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3 items-start">
                @forelse($objectives as $objective)
                    <div class="objective-card bg-white overflow-hidden shadow-sm rounded-lg hover:shadow-md transition-shadow duration-200" 
                         data-id="{{ $objective->id }}"
                         data-progress="{{ $objective->progress }}"
                         data-owner-id="{{ $objective->user_id }}"
                         data-owner-name="{{ $objective->user->name }}"
                         data-created="{{ $objective->created_at->format('Y-m-d') }}"
                         data-updated="{{ $objective->updated_at->format('Y-m-d') }}"
                         data-due-date="{{ $objective->end_date ? $objective->end_date->format('Y-m-d') : '' }}"
                         data-title="{{ strtolower($objective->title) }}"
                         data-cycle="{{ $objective->cycle_id ?? 'unassigned' }}"
                         data-current-user="{{ auth()->id() }}"
                         >
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-start flex-1 mr-3">
                                    <span class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-600 font-semibold text-lg flex-shrink-0">
                                        {{ $loop->iteration }}
                                    </span>
                                    <h3 class="ml-3 text-xl font-semibold text-gray-900 leading-tight">
                                        <a href="{{ route('objectives.show', $objective) }}" class="hover:text-blue-600 transition-colors duration-150">
                                            {{ $objective->title }}
                                        </a>
                                    </h3>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium flex-shrink-0 {{ $objective->progress >= 100 ? 'bg-green-100 text-green-800' : ($objective->progress >= 50 ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ $objective->progress }}%
                                </span>
                            </div>

                            <!-- Description with Read More -->
                            <div class="mb-4 h-20 flex flex-col">
                                @php
                                    $truncatedDesc = truncateDescription($objective->description, 120);
                                    $needsReadMore = Str::length($objective->description) > 120;
                                @endphp
                                <div class="flex-1 overflow-hidden">
                                    <p class="text-gray-600 text-sm leading-relaxed line-clamp-3">{{ $truncatedDesc }}</p>
                                </div>
                                @if($needsReadMore)
                                    <div class="mt-1">
                                        <a href="{{ route('objectives.show', $objective) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors duration-200 inline-block">
                                            Read more →
                                        </a>
                                    </div>
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
                                    <div class="font-semibold text-green-700">{{ $objective->keyResults->filter(function($kr) { return $kr->current_value >= $kr->target_value; })->count() }}</div>
                                    <div class="text-green-600">Completed KR</div>
                                </div>
                                <div class="bg-yellow-50 rounded-lg p-2.5 text-center">
                                    <div class="font-semibold text-yellow-700">{{ $objective->keyResults->filter(function($kr) { return $kr->current_value < $kr->target_value; })->count() }}</div>
                                    <div class="text-yellow-600">Pending KR</div>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">OKR Cycle</span>
                                    <span class="font-medium text-indigo-600">
                                        {{ $objective->cycle_id ?? 'Not assigned' }}
                                    </span>
                                </div>
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
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            overflow: hidden;
        }
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 3;
            overflow: hidden;
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

            // Filtering functionality
            const statusFilter = document.getElementById('status-filter');
            const timeFilter = document.getElementById('time-filter');
            const progressFilter = document.getElementById('progress-filter');
            const cycleFilter = document.getElementById('cycle-filter');
            const ownerFilter = document.getElementById('owner-filter');
            const clearFiltersBtn = document.getElementById('clear-filters');
            const resultsCount = document.getElementById('results-count');
            const sortAlphaBtn = document.getElementById('sort-alpha');
            const sortProgressBtn = document.getElementById('sort-progress');

            // Store original cards for filtering
            let allCards = Array.from(document.querySelectorAll('.objective-card'));
            let filteredCards = [...allCards];

            // Filter function
            function applyFilters() {
                const statusValue = statusFilter.value;
                const timeValue = timeFilter.value;
                const progressValue = progressFilter.value;
                const cycleValue = cycleFilter.value;
                const ownerValue = ownerFilter.value;
                const currentUserId = document.querySelector('.objective-card')?.dataset.currentUser;

                filteredCards = allCards.filter(card => {
                    const progress = parseFloat(card.dataset.progress);
                    const ownerId = card.dataset.ownerId;
                    const cycle = card.dataset.cycle;
                    const dueDate = card.dataset.dueDate;
                    const createdDate = card.dataset.created;
                    const updatedDate = card.dataset.updated;

                    // Status filter
                    if (statusValue !== 'all') {
                        if (statusValue === 'not-started' && progress !== 0) return false;
                        if (statusValue === 'in-progress' && (progress <= 0 || progress >= 100)) return false;
                        if (statusValue === 'completed' && progress < 100) return false;
                        if (statusValue === 'overdue') {
                            if (!dueDate) return false;
                            const now = new Date();
                            const due = new Date(dueDate);
                            if (due >= now) return false;
                        }
                    }

                    // Time filter
                    if (timeValue !== 'all') {
                        const now = new Date();
                        const created = new Date(createdDate);
                        const updated = new Date(updatedDate);
                        
                        if (timeValue === 'due-soon') {
                            if (!dueDate) return false;
                            const due = new Date(dueDate);
                            const daysDiff = (due - now) / (1000 * 60 * 60 * 24);
                            if (daysDiff < 0 || daysDiff > 7) return false;
                        }
                        
                        if (timeValue === 'due-month') {
                            if (!dueDate) return false;
                            const due = new Date(dueDate);
                            if (due.getMonth() !== now.getMonth() || due.getFullYear() !== now.getFullYear()) return false;
                        }
                    }

                    // Progress filter
                    if (progressValue !== 'all') {
                        if (progressValue === '0' && progress !== 0) return false;
                        if (progressValue === '1-25' && (progress < 1 || progress > 25)) return false;
                        if (progressValue === '26-50' && (progress < 26 || progress > 50)) return false;
                        if (progressValue === '51-75' && (progress < 51 || progress > 75)) return false;
                        if (progressValue === '76-99' && (progress < 76 || progress > 99)) return false;
                        if (progressValue === '100' && progress < 100) return false;
                    }

                    // Cycle filter
                    if (cycleValue !== 'all') {
                        if (cycleValue === 'unassigned' && cycle !== 'unassigned') return false;
                        if (cycleValue !== 'unassigned' && cycle !== cycleValue) return false;
                    }

                    // Owner filter
                    if (ownerValue !== 'all') {
                        if (ownerValue === 'mine' && ownerId !== currentUserId) return false;
                        if (ownerValue === 'others' && ownerId === currentUserId) return false;
                        if (ownerValue !== 'mine' && ownerValue !== 'others' && ownerId !== ownerValue) return false;
                    }

                    return true;
                });

                renderFilteredCards();
                updateResultsCount();
            }

            // Sorting functions
            function sortCards(criteria) {
                if (criteria === 'alpha') {
                    filteredCards.sort((a, b) => {
                        const titleA = a.dataset.title.toLowerCase();
                        const titleB = b.dataset.title.toLowerCase();
                        return titleA.localeCompare(titleB);
                    });
                } else if (criteria === 'progress') {
                    filteredCards.sort((a, b) => {
                        const progressA = parseFloat(a.dataset.progress);
                        const progressB = parseFloat(b.dataset.progress);
                        return progressB - progressA; // Descending order
                    });
                } else if (criteria === 'latest') {
                    filteredCards.sort((a, b) => {
                        const dateA = new Date(a.dataset.created);
                        const dateB = new Date(b.dataset.created);
                        return dateB - dateA; // Newest first
                    });
                } else if (criteria === 'oldest') {
                    filteredCards.sort((a, b) => {
                        const dateA = new Date(a.dataset.created);
                        const dateB = new Date(b.dataset.created);
                        return dateA - dateB; // Oldest first
                    });
                } else if (criteria === 'recently-updated') {
                    filteredCards.sort((a, b) => {
                        const dateA = new Date(a.dataset.updated);
                        const dateB = new Date(b.dataset.updated);
                        return dateB - dateA; // Most recently updated first
                    });
                }
                renderFilteredCards();
            }

            // Render filtered cards
            function renderFilteredCards() {
                const container = document.getElementById('objectives-container');
                
                // Hide all cards first
                allCards.forEach(card => {
                    card.style.display = 'none';
                });

                // Show filtered cards
                filteredCards.forEach(card => {
                    card.style.display = 'block';
                });

                // Reorder container children to match filtered cards order
                filteredCards.forEach(card => {
                    container.appendChild(card);
                });
            }

            // Update results count
            function updateResultsCount() {
                const total = allCards.length;
                const visible = filteredCards.length;
                resultsCount.textContent = visible === total 
                    ? `Showing all ${total} objectives`
                    : `Showing ${visible} of ${total} objectives`;
            }

            // Clear all filters
            function clearAllFilters() {
                statusFilter.value = 'all';
                timeFilter.value = 'all';
                progressFilter.value = 'all';
                cycleFilter.value = 'all';
                ownerFilter.value = 'all';
                
                filteredCards = [...allCards];
                renderFilteredCards();
                updateResultsCount();
                
                showNotification('Filters cleared', 'success');
            }

            // Apply time-based sorting when time filter changes
            function handleTimeFilter() {
                const timeValue = timeFilter.value;
                
                // Apply filters first
                applyFilters();
                
                // Then apply time-based sorting
                if (timeValue === 'latest' || timeValue === 'oldest' || timeValue === 'recently-updated') {
                    sortCards(timeValue);
                }
            }

            // Event listeners
            if (statusFilter) statusFilter.addEventListener('change', applyFilters);
            if (timeFilter) timeFilter.addEventListener('change', handleTimeFilter);
            if (progressFilter) progressFilter.addEventListener('change', applyFilters);
            if (cycleFilter) cycleFilter.addEventListener('change', applyFilters);
            if (ownerFilter) ownerFilter.addEventListener('change', applyFilters);
            if (clearFiltersBtn) clearFiltersBtn.addEventListener('click', clearAllFilters);
            if (sortAlphaBtn) sortAlphaBtn.addEventListener('click', () => sortCards('alpha'));
            if (sortProgressBtn) sortProgressBtn.addEventListener('click', () => sortCards('progress'));

            // Initialize with all cards visible
            updateResultsCount();
        });
    </script>
</x-app-layout> 