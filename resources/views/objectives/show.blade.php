<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 leading-tight">
                    {{ $objective->title }}
                </h2>
                <p class="mt-1 text-sm text-gray-600">Created {{ $objective->created_at->diffForHumans() }}</p>
            </div>
            <div class="flex items-center space-x-3">
                @can('update', $objective)
                    <a href="{{ route('objectives.edit', $objective) }}"
                       class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        {{ __('Edit Objective') }}
                    </a>
                @endcan
                @can('delete', $objective)
                    <form action="{{ route('objectives.destroy', $objective) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-white border border-red-300 rounded-lg text-sm font-medium text-red-700 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-150"
                                onclick="return confirm('Are you sure you want to delete this objective?')">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            {{ __('Delete') }}
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Objective Details -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Description') }}</h3>
                            <p class="text-gray-600">{{ $objective->description ?: 'No description provided.' }}</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Details') }}</h3>
                            <dl class="space-y-2">
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">OKR Cycle</dt>
                                    <dd class="font-medium text-indigo-600">
                                        @if($objective->cycle_id)
                                            <a href="{{ route('okr-cycles.index') }}" class="hover:text-indigo-700">
                                                {{ $objective->cycle_id }}
                                            </a>
                                        @else
                                            <span class="text-gray-500">Not assigned</span>
                                        @endif
                                    </dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Time Period</dt>
                                    <dd class="font-medium text-gray-900">{{ ucfirst($objective->time_period) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Start Date</dt>
                                    <dd class="font-medium text-gray-900">{{ $objective->start_date->format('M d, Y') }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">End Date</dt>
                                    <dd class="font-medium {{ $objective->end_date->isPast() ? 'text-red-600' : 'text-gray-900' }}">
                                        {{ $objective->end_date->format('M d, Y') }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Overall Progress -->
                    <div class="mt-8">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-lg font-medium text-gray-900">{{ __('Overall Progress') }}</h3>
                            <span class="text-lg font-semibold {{ $objective->progress >= 100 ? 'text-green-600' : 'text-blue-600' }}">
                                {{ $objective->progress }}%
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="h-3 rounded-full transition-all duration-300 ease-in-out {{ $objective->progress >= 100 ? 'bg-green-500' : 'bg-blue-500' }}"
                                 style="width: {{ $objective->progress }}%">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Key Results -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Key Results</h3>
                        <a href="{{ route('key-results.create', ['objective_id' => $objective->id]) }}"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-150 ease-in-out">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add Key Result
                        </a>
                    </div>

                    <div class="space-y-4">
                        @forelse($objective->keyResults as $keyResult)
                            <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors duration-150">
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <a href="{{ route('key-results.edit', $keyResult) }}" class="text-lg font-semibold text-gray-900 hover:text-indigo-600">{{ $keyResult->title }}</a>
                                        @if($keyResult->assignee)
                                            <span class="text-sm text-gray-500 ml-2">({{ $keyResult->assignee->name }})</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        @can('updateProgress', $keyResult)
                                            <button type="button"
                                                x-data
                                                @click="$dispatch('open-modal', 'update-progress-{{ $keyResult->id }}')"
                                                class="text-gray-600 hover:text-blue-600 transition-colors duration-150 p-1 rounded-full hover:bg-blue-100"
                                                title="Update Progress">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                            </button>
                                            <button type="button"
                                                onclick="markComplete('{{ $keyResult->id }}')"
                                                class="text-gray-600 hover:text-green-600 transition-colors duration-150 p-1 rounded-full hover:bg-green-100"
                                                title="Mark Complete">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            </button>
                                        @else
                                            <button type="button" onclick="alert('You do not have permission to update this Key Result\'s progress.')" class="text-gray-400 cursor-not-allowed p-1 rounded-full" title="Update Progress (disabled)">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                            </button>
                                            <button type="button" onclick="alert('You do not have permission to update this Key Result\'s progress.')" class="text-gray-400 cursor-not-allowed p-1 rounded-full" title="Mark Complete (disabled)">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            </button>
                                        @endcan

                                        @can('update', $keyResult)
                                            <a href="{{ route('key-results.edit', $keyResult) }}"
                                               class="text-gray-600 hover:text-blue-600 transition-colors duration-150 p-1 rounded-full hover:bg-blue-100"
                                               title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </a>
                                        @else
                                            <button type="button" onclick="alert('You do not have permission to edit this Key Result.')" class="text-gray-400 cursor-not-allowed p-1 rounded-full" title="Edit (disabled)">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>
                                        @endcan

                                        @can('delete', $keyResult)
                                            <form action="{{ route('key-results.destroy', $keyResult) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-gray-600 hover:text-red-600 transition-colors duration-150 p-1 rounded-full hover:bg-red-100"
                                                    title="Delete"
                                                    onclick="return confirm('Are you sure you want to delete this key result?')">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" onclick="alert('You do not have permission to delete this Key Result.')" class="text-gray-400 cursor-not-allowed p-1 rounded-full" title="Delete (disabled)">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        @endcan
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <div class="flex items-center">
                                        <div class="flex-grow">
                                            <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                                @php
                                                    if ($keyResult->target_value == 0) {
                                                        $progressPercent = $keyResult->current_value == 0 ? 100 : 0;
                                                    } else {
                                                        $progressPercent = ($keyResult->current_value / $keyResult->target_value) * 100;
                                                    }
                                                    $progressPercent = max(0, min(100, $progressPercent));
                                                @endphp
                                                <div class="h-2 {{ $progressPercent >= 100 ? 'bg-green-500' : 'bg-blue-500' }} rounded-full transition-all duration-300 ease-in-out"
                                                     style="width: {{ $progressPercent }}%">
                                                </div>
                                            </div>
                                        </div>
                                        <span class="ml-3 text-sm font-medium {{ $progressPercent >= 100 ? 'text-green-600' : 'text-blue-600' }}">
                                            {{ number_format($progressPercent, 1) }}%
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm">
                                        <div class="text-gray-600">
                                            Current: <span class="font-medium text-gray-900">{{ $keyResult->current_value }}</span>
                                            / Target: <span class="font-medium text-gray-900">{{ $keyResult->target_value }}</span>
                                        </div>
                                        <div class="text-gray-600">
                                            Owner: <span class="font-medium text-gray-900">{{ $keyResult->owner->name }}</span>
                                        </div>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        Last Updated: {{ $keyResult->updated_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>

                            <!-- Progress Update Modal -->
                            <x-modal name="update-progress-{{ $keyResult->id }}" :show="false" maxWidth="md">
                                <form method="POST" action="{{ route('key-results.update-progress', $keyResult) }}" class="p-6">
                                    @csrf
                                    @method('PATCH')

                                    <h2 class="text-lg font-medium text-gray-900 mb-4">
                                        Update Progress for "{{ $keyResult->title }}"
                                    </h2>

                                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="text-sm text-gray-600">Current Progress</span>
                                            @php
                                                if ($keyResult->target_value == 0) {
                                                    $currentProgressPercent = $keyResult->current_value == 0 ? 100 : 0;
                                                } else {
                                                    $currentProgressPercent = ($keyResult->current_value / $keyResult->target_value) * 100;
                                                }
                                                $currentProgressPercent = max(0, min(100, $currentProgressPercent));
                                            @endphp
                                            <span class="text-sm font-medium {{ $currentProgressPercent >= 100 ? 'text-green-600' : 'text-blue-600' }}">
                                                {{ number_format($currentProgressPercent, 1) }}%
                                            </span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600">Target Value</span>
                                            <span class="text-sm font-medium text-gray-900">{{ $keyResult->target_value }}</span>
                                        </div>
                                    </div>

                                    <div>
                                        <x-input-label for="current_value_{{ $keyResult->id }}" :value="__('Current Value')" />
                                        <x-text-input
                                            id="current_value_{{ $keyResult->id }}"
                                            name="current_value"
                                            type="number"
                                            class="mt-1 block w-full"
                                            :value="old('current_value', $keyResult->current_value)"
                                            required
                                            min="0"
                                            max="{{ $keyResult->target_value }}"
                                            step="0.01"
                                            autofocus
                                        />
                                        <div class="mt-2 flex space-x-2">
                                            <button type="button"
                                                class="quick-value-btn px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded-md transition-colors duration-150"
                                                data-target="{{ $keyResult->target_value }}"
                                                data-value="25">25%</button>
                                            <button type="button"
                                                class="quick-value-btn px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded-md transition-colors duration-150"
                                                data-target="{{ $keyResult->target_value }}"
                                                data-value="50">50%</button>
                                            <button type="button"
                                                class="quick-value-btn px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded-md transition-colors duration-150"
                                                data-target="{{ $keyResult->target_value }}"
                                                data-value="75">75%</button>
                                            <button type="button"
                                                class="quick-value-btn px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded-md transition-colors duration-150"
                                                data-target="{{ $keyResult->target_value }}"
                                                data-value="100">100%</button>
                                        </div>
                                        <x-input-error class="mt-2" :messages="$errors->get('current_value')" />
                                    </div>

                                    <div class="mt-6 flex justify-end space-x-3">
                                        <x-secondary-button type="button" @click="$dispatch('close-modal', 'update-progress-{{ $keyResult->id }}')">
                                            {{ __('Cancel') }}
                                        </x-secondary-button>

                                        <x-primary-button type="submit">
                                            {{ __('Update Progress') }}
                                        </x-primary-button>
                                    </div>
                                </form>
                            </x-modal>
                        @empty
                            <div class="text-center py-8">
                                <p class="text-gray-500 mb-4">No key results defined yet</p>
                                <a href="{{ route('key-results.create', ['objective_id' => $objective->id]) }}"
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-150 ease-in-out">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Add Your First Key Result
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Tasks Section -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Tasks') }}</h3>
                        <a href="{{ route('tasks.create', ['objective_id' => $objective->id]) }}"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-150 ease-in-out">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            {{ __('Add Task') }}
                        </a>
                    </div>

                    <div class="space-y-4">
                        @forelse($objective->tasks as $task)
                            <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors duration-150">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ $task->title }}</h4>
                                        <p class="text-gray-600 mt-1">{{ $task->description }}</p>
                                        <div class="mt-2 flex items-center space-x-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $task->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ ucfirst($task->status) }}
                                            </span>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Due: {{ $task->due_date?->format('M d, Y') ?? 'No due date' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        @if($task->status !== 'completed')
                                            <form action="{{ route('tasks.complete', $task) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="text-gray-600 hover:text-green-600 transition-colors duration-150 p-1 rounded-full hover:bg-green-100"
                                                    title="Complete Task">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('tasks.edit', $task) }}"
                                           class="text-gray-600 hover:text-blue-600 transition-colors duration-150 p-1 rounded-full hover:bg-blue-100"
                                           title="Edit Task">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <p class="text-gray-500 mb-4">{{ __('No tasks created yet.') }}</p>
                                <a href="{{ route('tasks.create', ['objective_id' => $objective->id]) }}"
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-150 ease-in-out">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Create Your First Task
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Function to mark key result as complete
            window.markComplete = function(keyResultId) {
                if (confirm('Are you sure you want to mark this key result as complete?')) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/key-results/${keyResultId}/complete`;

                    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;

                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'PATCH';

                    form.appendChild(csrfInput);
                    form.appendChild(methodInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            };

            // Handle quick value buttons
            document.querySelectorAll('.quick-value-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const percentage = parseInt(this.dataset.value);
                    const targetValue = parseFloat(this.dataset.target) || 100;
                    const currentValueInput = this.closest('form').querySelector('input[name="current_value"]');
                    const newValue = (percentage / 100) * targetValue;
                    currentValueInput.value = newValue.toFixed(2);
                });
            });
        });
    </script>
    @endpush
</x-app-layout> 