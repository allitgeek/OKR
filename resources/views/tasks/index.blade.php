<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tasks') }}
            </h2>
            <button onclick="window.location.href='{{ route('tasks.create') }}'" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                {{ __('New Task') }}
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Task Filters -->
                    <div class="mb-6">
                        <div class="flex items-center space-x-4">
                            <button onclick="window.location.href='{{ route('tasks.index') }}'" class="px-4 py-2 rounded-md {{ request()->missing('status') ? 'bg-blue-100 text-blue-800' : 'text-gray-600 hover:text-gray-900' }}">
                                All
                            </button>
                            <button onclick="window.location.href='{{ route('tasks.index', ['status' => 'pending']) }}'" class="px-4 py-2 rounded-md {{ request('status') === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'text-gray-600 hover:text-gray-900' }}">
                                Pending
                            </button>
                            <button onclick="window.location.href='{{ route('tasks.index', ['status' => 'in_progress']) }}'" class="px-4 py-2 rounded-md {{ request('status') === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'text-gray-600 hover:text-gray-900' }}">
                                In Progress
                            </button>
                            <button onclick="window.location.href='{{ route('tasks.index', ['status' => 'completed']) }}'" class="px-4 py-2 rounded-md {{ request('status') === 'completed' ? 'bg-green-100 text-green-800' : 'text-gray-600 hover:text-gray-900' }}">
                                Completed
                            </button>
                        </div>
                    </div>

                    <!-- Tasks List -->
                    <div class="space-y-6">
                        @forelse($tasks as $task)
                            <div class="border rounded-lg p-6">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h3 class="text-xl font-medium">
                                            <a href="{{ route('tasks.show', $task) }}" class="text-blue-600 hover:text-blue-800">
                                                {{ $task->title }}
                                            </a>
                                        </h3>
                                        <p class="mt-1 text-gray-600">{{ $task->description }}</p>

                                        <!-- Task Metadata -->
                                        <div class="mt-4 flex items-center space-x-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $task->status === 'completed' ? 'bg-green-100 text-green-800' : ($task->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ ucfirst($task->status) }}
                                            </span>
                                            <span class="text-sm text-gray-500">
                                                Due: {{ $task->due_date?->format('M d, Y') ?? 'No due date' }}
                                            </span>
                                            <span class="text-sm text-gray-500">
                                                Assigned to: {{ $task->assignee->name }}
                                            </span>
                                        </div>

                                        <!-- Related Objective and Key Result -->
                                        <div class="mt-4 text-sm text-gray-500">
                                            <a href="{{ route('objectives.show', $task->keyResult->objective) }}" class="text-blue-600 hover:text-blue-800">
                                                {{ $task->keyResult->objective->title }}
                                            </a>
                                            <span class="mx-2">&rarr;</span>
                                            <span>{{ $task->keyResult->title }}</span>
                                        </div>
                                    </div>

                                    <div class="flex space-x-2 ml-4">
                                        @if($task->status !== 'completed')
                                            <form action="{{ route('tasks.complete', $task) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-500 hover:text-green-700">{{ __('Complete') }}</button>
                                            </form>
                                        @endif
                                        <button onclick="window.location.href='{{ route('tasks.edit', $task) }}'" class="text-blue-500 hover:text-blue-700">
                                            {{ __('Edit') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('No Tasks Found') }}</h3>
                                <p class="text-gray-600 mb-4">{{ __('Get started by creating your first task.') }}</p>
                                <button onclick="window.location.href='{{ route('tasks.create') }}'" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    {{ __('Create Task') }}
                                </button>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if($tasks->hasPages())
                        <div class="mt-6">
                            {{ $tasks->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 