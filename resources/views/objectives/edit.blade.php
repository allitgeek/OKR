<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Objective') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('objectives.update', $objective) }}" class="space-y-6" id="objective-form">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="title" :value="__('Title')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $objective->title)" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="3" required>{{ old('description', $objective->description) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <div>
                            <x-input-label for="start_date" :value="__('Start Date')" />
                            <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" :value="old('start_date', $objective->start_date?->format('Y-m-d'))" />
                            <x-input-error class="mt-2" :messages="$errors->get('start_date')" />
                        </div>

                        <div>
                            <x-input-label for="end_date" :value="__('Due Date')" />
                            <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full" :value="old('end_date', $objective->end_date?->format('Y-m-d'))" />
                            <x-input-error class="mt-2" :messages="$errors->get('end_date')" />
                        </div>

                        <div>
                            <x-input-label for="time_period" :value="__('Time Period')" />
                            <select id="time_period" name="time_period" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="monthly" {{ old('time_period', $objective->time_period) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="quarterly" {{ old('time_period', $objective->time_period) == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                <option value="yearly" {{ old('time_period', $objective->time_period) == 'yearly' ? 'selected' : '' }}>Yearly</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('time_period')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Update Objective') }}</x-primary-button>
                            <a href="{{ route('objectives.show', $objective) }}" class="text-gray-600 hover:text-gray-900">{{ __('Cancel') }}</a>
                        </div>
                    </form>

                    <!-- Key Results Section -->
                    <div class="mt-8 pt-8 border-t">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Key Results') }}</h3>
                        
                        <form method="POST" action="{{ route('objectives.key-results.store', $objective) }}" class="space-y-6">
                            @csrf

                            <div>
                                <x-input-label for="key_result_title" :value="__('Title')" />
                                <x-text-input id="key_result_title" name="title" type="text" class="mt-1 block w-full" required />
                                <x-input-error class="mt-2" :messages="$errors->get('title')" />
                            </div>

                            <div>
                                <x-input-label for="owner_id" :value="__('Assign To')" />
                                <select id="owner_id" name="owner_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ $user->id === auth()->id() ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('owner_id')" />
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="current_value" :value="__('Current Value')" />
                                    <x-text-input id="current_value" name="current_value" type="number" step="0.01" class="mt-1 block w-full" :value="old('current_value', 0)" required />
                                    <div class="mt-2 flex space-x-2">
                                        <button type="button" class="quick-value-btn px-2 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded" data-value="25">25%</button>
                                        <button type="button" class="quick-value-btn px-2 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded" data-value="50">50%</button>
                                        <button type="button" class="quick-value-btn px-2 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded" data-value="75">75%</button>
                                        <button type="button" class="quick-value-btn px-2 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded" data-value="100">100%</button>
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get('current_value')" />
                                </div>

                                <div>
                                    <x-input-label for="target_value" :value="__('Target Value')" />
                                    <x-text-input id="target_value" name="target_value" type="number" step="0.01" class="mt-1 block w-full" :value="old('target_value', 100)" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('target_value')" />
                                </div>
                            </div>

                            <div>
                                <x-primary-button>{{ __('Add Key Result') }}</x-primary-button>
                            </div>
                        </form>

                        <div class="mt-6 space-y-4">
                            @foreach($objective->keyResults as $keyResult)
                                @php
                                    if ($keyResult->target_value == 0) {
                                        $progressPercent = $keyResult->current_value == 0 ? 100 : 0;
                                    } else {
                                        $progressPercent = ($keyResult->current_value / $keyResult->target_value) * 100;
                                    }
                                    $progressPercent = max(0, min(100, $progressPercent));
                                    $isComplete = $progressPercent >= 100;
                                    $progressColor = $isComplete ? 'bg-green-600' : 'bg-blue-600';
                                @endphp
                                <div class="bg-gray-50 p-4 rounded-lg" data-kr-due-date="{{ $keyResult->due_date?->format('Y-m-d') }}">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-grow">
                                            <h4 class="font-medium">{{ $keyResult->title }}</h4>
                                            <div class="mt-2">
                                                <div class="flex items-center">
                                                    <div class="flex-grow">
                                                        <div class="h-2 bg-gray-200 rounded-full">
                                                            <div class="h-2 {{ $progressColor }} rounded-full" style="width: {{ $progressPercent }}%"></div>
                                                        </div>
                                                    </div>
                                                    <span class="ml-2 text-sm text-gray-600">
                                                        {{ number_format($progressPercent, 1) }}%
                                                    </span>
                                                </div>
                                                <div class="mt-1 text-sm text-gray-600">
                                                    {{ $keyResult->current_value }} / {{ $keyResult->target_value }}
                                                </div>
                                            </div>
                                            <div class="mt-2 text-sm text-gray-600">
                                                <span>Owner: {{ $keyResult->owner->name }}</span>
                                                <span class="ml-4">Last Updated: {{ $keyResult->updated_at->diffForHumans() }}</span>
                                                @if($keyResult->due_date)
                                                    <span class="ml-4">Due: {{ $keyResult->due_date->format('M d, Y') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="ml-4 bg-yellow-200 rounded-lg p-2 flex items-center space-x-2">
                                            <button type="button" onclick="window.location.href='{{ route('key-results.edit', $keyResult) }}'" class="text-gray-600 hover:text-gray-800" title="Update Progress">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                </svg>
                                            </button>
                                            <button type="button" onclick="markComplete('{{ $keyResult->id }}')" class="text-gray-600 hover:text-green-600" title="Mark Complete">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </button>
                                            <a href="{{ route('key-results.edit', $keyResult) }}" class="text-gray-600 hover:text-blue-600" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                            <form action="{{ route('key-results.destroy', $keyResult) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-gray-600 hover:text-red-600" title="Delete">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
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

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle quick value buttons
        const currentValueInput = document.getElementById('current_value');
        const targetValueInput = document.getElementById('target_value');
        
        document.querySelectorAll('.quick-value-btn').forEach(button => {
            button.addEventListener('click', function() {
                const percentage = parseInt(this.dataset.value);
                const targetValue = parseFloat(targetValueInput.value) || 100;
                const newValue = (percentage / 100) * targetValue;
                currentValueInput.value = newValue;
            });
        });

        // Update quick value buttons when target value changes
        targetValueInput.addEventListener('change', function() {
            const currentPercentage = (parseFloat(currentValueInput.value) / parseFloat(this.value)) * 100;
            currentValueInput.value = (currentPercentage / 100) * parseFloat(this.value);
        });

        // Due date validation
        const endDateInput = document.getElementById('end_date');
        const objectiveForm = document.getElementById('objective-form');
        
        objectiveForm.addEventListener('submit', function(e) {
            const newEndDate = endDateInput.value;
            if (!newEndDate) return;
            
            const newEndDateTime = new Date(newEndDate);
            const conflictingKRs = [];
            
            // Check all KR due dates
            document.querySelectorAll('[data-kr-due-date]').forEach(function(krElement) {
                const krDueDate = krElement.getAttribute('data-kr-due-date');
                if (krDueDate && krDueDate !== 'null') {
                    const krDueDateTime = new Date(krDueDate);
                    if (krDueDateTime > newEndDateTime) {
                        const krTitle = krElement.querySelector('h4').textContent;
                        conflictingKRs.push({
                            title: krTitle,
                            dueDate: krDueDate
                        });
                    }
                }
            });
            
            if (conflictingKRs.length > 0) {
                e.preventDefault();
                
                let message = `Cannot save objective due date because the following Key Results have due dates that are after the new objective due date:\n\n`;
                conflictingKRs.forEach(kr => {
                    message += `• ${kr.title} (Due: ${new Date(kr.dueDate).toLocaleDateString()})\n`;
                });
                message += `\nPlease update these Key Result due dates first, then try again.`;
                
                alert(message);
                return false;
            }
        });

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
    });
    </script>
    @endpush
</x-app-layout> 