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

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Task Filters -->
            <div class="mb-8">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-1">
                    <div class="flex flex-wrap items-center gap-1">
                        <a href="{{ route('tasks.index') }}" class="px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 {{ request()->missing('status') ? 'bg-blue-500 text-white' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                            All
                        </a>
                        <a href="{{ route('tasks.index', ['status' => 'pending']) }}" class="px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 {{ request('status') === 'pending' ? 'bg-yellow-500 text-white' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                            Pending
                        </a>
                        <a href="{{ route('tasks.index', ['status' => 'in_progress']) }}" class="px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 {{ request('status') === 'in_progress' ? 'bg-blue-500 text-white' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                            In Progress
                        </a>
                        <a href="{{ route('tasks.index', ['status' => 'completed']) }}" class="px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 {{ request('status') === 'completed' ? 'bg-green-500 text-white' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                            Completed
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tasks Grid -->
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @forelse($tasks as $task)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg transition-shadow duration-300">
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
                            
                            <p class="text-gray-600 text-sm line-clamp-3 mb-4">{{ $task->description }}</p>
                        </div>

                        <!-- Task Metadata -->
                        <div class="px-6 py-4 bg-gray-50 rounded-b-xl">
                            <div class="space-y-2 text-sm">
                                <!-- Due Date -->
                                <div class="flex items-center text-gray-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span>{{ $task->due_date?->format('M d, Y') ?? 'No due date' }}</span>
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
    </style>
</x-app-layout> 