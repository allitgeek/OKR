<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $task->title }}
            </h2>
            <div class="flex space-x-4">
                @if($task->status !== 'completed')
                    <form action="{{ route('tasks.complete', $task) }}" method="POST" class="inline">
                        @csrf
                        <x-primary-button>{{ __('Mark as Complete') }}</x-primary-button>
                    </form>
                @endif
                <button onclick="window.location.href='{{ route('tasks.edit', $task) }}'" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    {{ __('Edit Task') }}
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Task Details -->
                    <div class="mb-8">
                        <div class="mb-4">
                            <h3 class="text-lg font-medium text-gray-900">{{ __('Description') }}</h3>
                            <p class="mt-1 text-gray-600">{{ $task->description }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">{{ __('Status') }}</h3>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $task->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($task->status) }}
                                </span>
                            </div>

                            <div>
                                <h3 class="text-lg font-medium text-gray-900">{{ __('Due Date') }}</h3>
                                <p class="mt-1 text-gray-600">{{ $task->due_date?->format('M d, Y') ?? 'No due date set' }}</p>
                            </div>

                            <div>
                                <h3 class="text-lg font-medium text-gray-900">{{ __('Assigned To') }}</h3>
                                <p class="mt-1 text-gray-600">{{ $task->assignee->name }}</p>
                            </div>

                            <div>
                                <h3 class="text-lg font-medium text-gray-900">{{ __('Created By') }}</h3>
                                <p class="mt-1 text-gray-600">{{ $task->creator->name }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Related Objective and Key Result -->
                    <div class="border-t pt-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Related Objective & Key Result') }}</h3>
                        
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="mb-4">
                                <h4 class="font-medium text-gray-700">{{ __('Objective') }}</h4>
                                <a href="{{ route('objectives.show', $task->keyResult->objective) }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $task->keyResult->objective->title }}
                                </a>
                            </div>

                            <div>
                                <h4 class="font-medium text-gray-700">{{ __('Key Result') }}</h4>
                                <p class="text-gray-600">{{ $task->keyResult->title }}</p>
                                <div class="mt-2">
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $task->keyResult->progress }}%"></div>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-600">
                                        Progress: {{ $task->keyResult->progress }}%
                                        ({{ $task->keyResult->current_value }} / {{ $task->keyResult->target_value }})
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Task History -->
                    <div class="border-t pt-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Task History') }}</h3>
                        
                        <div class="space-y-4">
                            @foreach($task->activities as $activity)
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                            <span class="text-gray-500 text-sm">{{ substr($activity->causer->name ?? 'System', 0, 1) }}</span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-sm">
                                            <span class="font-medium text-gray-900">{{ $activity->causer->name ?? 'System' }}</span>
                                            <span class="text-gray-500">{{ $activity->description }}</span>
                                        </div>
                                        <div class="mt-1 text-sm text-gray-500">
                                            {{ $activity->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 