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
    function truncateDescription($description, $length = 120) {
        return Str::length($description) > $length ? Str::limit($description, $length, '...') : $description;
    }
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tasks') }}
            </h2>
            <a href="{{ route('tasks.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                {{ __('New Task') }}
            </a>
        </div>
    </x-slot>

    <!-- Add Sortable.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Enhanced Task Filters -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-sm font-medium text-gray-700 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"></path>
                        </svg>
                        Task Filters
                    </h4>
                    <button id="clear-filters" class="text-sm text-gray-500 hover:text-gray-700 font-medium">Clear All</button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Status Filter -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                        <select id="status-filter" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="all">All Tasks</option>
                            <option value="assigned">Assigned</option>
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="overdue">Overdue</option>
                        </select>
                    </div>
                    
                    <!-- Priority Filter -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Priority</label>
                        <select id="priority-filter" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="all">All Priorities</option>
                            <option value="high">High Priority</option>
                            <option value="medium">Medium Priority</option>
                            <option value="low">Low Priority</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                    
                    <!-- Time Filter -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Time</label>
                        <select id="time-filter" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="all">All Time</option>
                            <option value="latest">Latest Created</option>
                            <option value="oldest">Oldest Created</option>
                            <option value="due-today">Due Today</option>
                            <option value="due-soon">Due Soon (3 days)</option>
                            <option value="due-week">Due This Week</option>
                            <option value="recently-updated">Recently Updated</option>
                        </select>
                    </div>
                    
                    <!-- Assignee Filter -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Assignee</label>
                        <select id="assignee-filter" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="all">All Assignees</option>
                            <option value="mine">My Tasks</option>
                            <option value="others">Others</option>
                            @foreach($tasks->pluck('assignee')->unique() as $assignee)
                                <option value="{{ $assignee->id }}">{{ $assignee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <!-- Filter Results Info -->
                <div id="filter-results" class="mt-3 text-sm text-gray-600 flex items-center justify-between">
                    <span id="results-count">Showing all {{ $tasks->count() }} tasks</span>
                    <div class="flex space-x-2">
                        <button id="sort-alpha" class="px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded transition-colors" title="Sort A-Z">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path>
                            </svg>
                        </button>
                        <button id="sort-due-date" class="px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded transition-colors" title="Sort by Due Date">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tasks Grid -->
            <div id="tasks-container" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @forelse($tasks as $task)
                    <div class="task-card bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg transition-shadow duration-300" 
                         data-id="{{ $task->id }}"
                         data-status="{{ $task->status }}"
                         data-assignee-id="{{ $task->assignee_id }}"
                         data-assignee-name="{{ $task->assignee->name }}"
                         data-created="{{ $task->created_at->format('Y-m-d') }}"
                         data-updated="{{ $task->updated_at->format('Y-m-d') }}"
                         data-due-date="{{ $task->due_date ? $task->due_date->format('Y-m-d') : '' }}"
                         data-title="{{ strtolower($task->title) }}"
                         data-current-user="{{ auth()->id() }}"
                         data-priority="medium"
                         >
                        <!-- Task Header -->
                        <div class="p-6 pb-4">
                            <div class="flex justify-between items-start mb-3">
                                <h3 class="text-lg font-semibold text-gray-900 line-clamp-2">
                                    <a href="{{ route('tasks.show', $task) }}" class="hover:text-blue-600 transition-colors duration-200">
                                        {{ $task->title }}
                                    </a>
                                </h3>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium ml-2 {{ 
                                    $task->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                    ($task->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 
                                    ($task->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) 
                                }}">
                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                </span>
                            </div>
                            
                            <!-- Description with Read More -->
                            <div class="mb-4">
                                @php
                                    $truncatedDesc = truncateDescription($task->description, 120);
                                    $needsReadMore = Str::length($task->description) > 120;
                                @endphp
                                <p class="text-gray-600 text-sm">{{ $truncatedDesc }}</p>
                                @if($needsReadMore)
                                    <a href="{{ route('tasks.show', $task) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors duration-200">
                                        Read more →
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Task Metadata -->
                        <div class="px-6 py-4 bg-gray-50 rounded-b-xl">
                            <div class="space-y-2 text-sm">
                                <!-- Due Date -->
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="{{ getDueDateColor($task->due_date) }}">{{ $task->due_date?->format('M d, Y') ?? 'No due date' }}</span>
                                </div>

                                <!-- Assignee -->
                                <div class="flex items-center text-gray-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span>{{ $task->assignee->name }}</span>
                                </div>

                                <!-- Related Objective and Key Result -->
                                @if($task->keyResult)
                                    <div class="flex items-start text-gray-600">
                                        <svg class="w-4 h-4 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                        </svg>
                                        <div class="min-w-0">
                                            <a href="{{ route('objectives.show', $task->keyResult->objective) }}" class="text-blue-600 hover:text-blue-800 font-medium text-xs">
                                                {{ Str::limit($task->keyResult->objective->title, 30) }}
                                            </a>
                                            <div class="text-xs text-gray-500">{{ Str::limit($task->keyResult->title, 35) }}</div>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center text-gray-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="italic text-xs">Not linked to any key result</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex justify-end space-x-2 mt-4 pt-4 border-t border-gray-200">
                                @if($task->status !== 'completed')
                                    <form action="{{ route('tasks.complete', $task) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-green-100 hover:bg-green-200 text-green-700 text-xs font-medium rounded-md transition-colors duration-200">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Complete
                                        </button>
                                    </form>
                                @endif
                                
                                <a href="{{ route('tasks.edit', $task) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 text-xs font-medium rounded-md transition-colors duration-200">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit
                                </a>

                                <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this task? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 text-xs font-medium rounded-md transition-colors duration-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full">
                        <div class="text-center py-16">
                            <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('No Tasks Found') }}</h3>
                            <p class="text-gray-600 mb-6">{{ __('Get started by creating your first task.') }}</p>
                            <a href="{{ route('tasks.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                                {{ __('Create Task') }}
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($tasks->hasPages())
                <div class="mt-8">
                    {{ $tasks->links() }}
                </div>
            @endif
        </div>
    </div>

    <style>
        .line-clamp-2 {
            overflow: hidden;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
        }
        .line-clamp-3 {
            overflow: hidden;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 3;
        }

        /* Drag and drop styles */
        .sortable-ghost {
            opacity: 0.4;
        }
        .sortable-chosen {
            cursor: grabbing;
        }
        .task-card {
            cursor: grab;
        }
        .task-card:hover {
            cursor: grab;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
                        localStorage.setItem('tasks_page_order', JSON.stringify(taskIds));
                        
                        // Show success message
                        showNotification('Tasks reordered successfully!', 'success');
                    }
                });

                // Restore order from localStorage
                const savedOrder = localStorage.getItem('tasks_page_order');
                if (savedOrder) {
                    try {
                        const order = JSON.parse(savedOrder);
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

            // Task filtering functionality
            const statusFilter = document.getElementById('status-filter');
            const priorityFilter = document.getElementById('priority-filter');
            const timeFilter = document.getElementById('time-filter');
            const assigneeFilter = document.getElementById('assignee-filter');
            const clearFiltersBtn = document.getElementById('clear-filters');
            const resultsCount = document.getElementById('results-count');
            const sortAlphaBtn = document.getElementById('sort-alpha');
            const sortDueDateBtn = document.getElementById('sort-due-date');

            // Store original cards for filtering
            let allCards = Array.from(document.querySelectorAll('.task-card'));
            let filteredCards = [...allCards];

            // Filter function
            function applyFilters() {
                const statusValue = statusFilter.value;
                const priorityValue = priorityFilter.value;
                const timeValue = timeFilter.value;
                const assigneeValue = assigneeFilter.value;
                const currentUserId = document.querySelector('.task-card')?.dataset.currentUser;

                filteredCards = allCards.filter(card => {
                    const status = card.dataset.status;
                    const assigneeId = card.dataset.assigneeId;
                    const dueDate = card.dataset.dueDate;
                    const createdDate = card.dataset.created;
                    const updatedDate = card.dataset.updated;
                    const priority = card.dataset.priority || 'medium';

                    // Status filter
                    if (statusValue !== 'all') {
                        if (statusValue === 'overdue') {
                            if (!dueDate) return false;
                            const now = new Date();
                            const due = new Date(dueDate);
                            if (due >= now || status === 'completed') return false;
                        } else if (status !== statusValue) {
                            return false;
                        }
                    }

                    // Priority filter
                    if (priorityValue !== 'all') {
                        if (priority !== priorityValue) return false;
                    }

                    // Time filter
                    if (timeValue !== 'all') {
                        const now = new Date();
                        const created = new Date(createdDate);
                        const updated = new Date(updatedDate);
                        
                        if (timeValue === 'due-today') {
                            if (!dueDate) return false;
                            const due = new Date(dueDate);
                            const today = new Date();
                            today.setHours(0, 0, 0, 0);
                            due.setHours(0, 0, 0, 0);
                            if (due.getTime() !== today.getTime()) return false;
                        }
                        
                        if (timeValue === 'due-soon') {
                            if (!dueDate) return false;
                            const due = new Date(dueDate);
                            const daysDiff = (due - now) / (1000 * 60 * 60 * 24);
                            if (daysDiff < 0 || daysDiff > 3) return false;
                        }
                        
                        if (timeValue === 'due-week') {
                            if (!dueDate) return false;
                            const due = new Date(dueDate);
                            const weekFromNow = new Date(now.getTime() + (7 * 24 * 60 * 60 * 1000));
                            if (due < now || due > weekFromNow) return false;
                        }
                    }

                    // Assignee filter
                    if (assigneeValue !== 'all') {
                        if (assigneeValue === 'mine' && assigneeId !== currentUserId) return false;
                        if (assigneeValue === 'others' && assigneeId === currentUserId) return false;
                        if (assigneeValue !== 'mine' && assigneeValue !== 'others' && assigneeId !== assigneeValue) return false;
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
                } else if (criteria === 'due-date') {
                    filteredCards.sort((a, b) => {
                        const dueDateA = a.dataset.dueDate;
                        const dueDateB = b.dataset.dueDate;
                        
                        // Handle empty due dates - put them at the end
                        if (!dueDateA && !dueDateB) return 0;
                        if (!dueDateA) return 1;
                        if (!dueDateB) return -1;
                        
                        const dateA = new Date(dueDateA);
                        const dateB = new Date(dueDateB);
                        return dateA - dateB; // Earliest due date first
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
                const container = document.getElementById('tasks-container');
                
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
                    ? `Showing all ${total} tasks`
                    : `Showing ${visible} of ${total} tasks`;
            }

            // Clear all filters
            function clearAllFilters() {
                statusFilter.value = 'all';
                priorityFilter.value = 'all';
                timeFilter.value = 'all';
                assigneeFilter.value = 'all';
                
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

            // Event listeners for task filters
            if (statusFilter) statusFilter.addEventListener('change', applyFilters);
            if (priorityFilter) priorityFilter.addEventListener('change', applyFilters);
            if (timeFilter) timeFilter.addEventListener('change', handleTimeFilter);
            if (assigneeFilter) assigneeFilter.addEventListener('change', applyFilters);
            if (clearFiltersBtn) clearFiltersBtn.addEventListener('click', clearAllFilters);
            if (sortAlphaBtn) sortAlphaBtn.addEventListener('click', () => sortCards('alpha'));
            if (sortDueDateBtn) sortDueDateBtn.addEventListener('click', () => sortCards('due-date'));

            // Initialize with all cards visible
            updateResultsCount();
        });
    </script>
</x-app-layout> 