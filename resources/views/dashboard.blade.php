@php
    use Illuminate\Support\Str;
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div x-data="{ show: true }" 
                     x-show="show" 
                     x-init="setTimeout(() => show = false, 3000)"
                     class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" 
                     role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                    <button @click="show = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <title>Close</title>
                            <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                        </svg>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div x-data="{ show: true }" 
                     x-show="show" 
                     x-init="setTimeout(() => show = false, 3000)"
                     class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" 
                     role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                    <button @click="show = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <title>Close</title>
                            <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                        </svg>
                    </button>
                </div>
            @endif

            @can('manage-users')
            <!-- Action Buttons -->
            <div class="mb-4">
                <button x-data @click="$dispatch('open-modal', 'create-objective')" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create New Objective
                </button>
                <button x-data @click="$dispatch('open-modal', 'create-task')" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded ml-2 inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create New Task
                </button>
            </div>
            @endcan

            <!-- Objectives Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Objectives</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Owner</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time Period</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($objectives as $objective)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $objective->title }}</div>
                                            <div class="text-sm text-gray-500">{{ Str::limit($objective->description, 50) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $objective->user->name }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="w-48">
                                                <div class="flex items-center">
                                                    <div class="flex-grow">
                                                        <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                                            <div class="h-2 bg-blue-600 rounded-full" style="width: {{ $objective->progress }}%"></div>
                                                        </div>
                                                    </div>
                                                    <span class="ml-2 text-sm text-gray-600">
                                                        {{ number_format($objective->progress, 1) }}%
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $objective->time_period ? ucfirst($objective->time_period) : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $objective->end_date ? $objective->end_date->format('M d, Y') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <a href="{{ route('objectives.show', $objective) }}" 
                                                class="text-blue-600 hover:text-blue-900 inline-flex items-center"
                                                title="View objective details">
                                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tasks Section -->
            @include('tasks.list')
        </div>
    </div>

    <!-- Create Objective Modal -->
    <x-modal name="create-objective" :show="false" maxWidth="lg">
        <form method="POST" action="{{ route('objectives.create.super') }}" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Create New Objective') }}
            </h2>

            <div class="mt-6">
                <x-input-label for="title" :value="__('Title')" />
                <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" required />
                <x-input-error class="mt-2" :messages="$errors->get('title')" />
            </div>

            <div class="mt-6">
                <x-input-label for="description" :value="__('Description')" />
                <textarea id="description" name="description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="3" required></textarea>
                <x-input-error class="mt-2" :messages="$errors->get('description')" />
            </div>

            <div class="mt-6">
                <x-input-label for="user_search" :value="__('Search Assignee')" />
                <input type="text" id="user_search" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Type to search users...">

                <x-input-label for="user_id" :value="__('Assign To')" class="mt-2" />
                <select id="user_id" name="user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ $user->id === auth()->id() ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('user_id')" />
            </div>

            <div class="mt-6 grid grid-cols-2 gap-4">
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

            <div class="mt-6 flex justify-end">
                <x-secondary-button @click="$dispatch('close-modal', 'create-objective')" class="mr-3">
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
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Create New Task') }}
            </h2>

            <div class="mt-6">
                <x-input-label for="title" :value="__('Title')" />
                <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" required autofocus />
                <x-input-error class="mt-2" :messages="$errors->get('title')" />
            </div>

            <div class="mt-6">
                <x-input-label for="description" :value="__('Description')" />
                <textarea id="description" name="description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="3" required>{{ old('description') }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('description')" />
            </div>

            <div class="mt-6">
                <x-input-label for="key_result_id" :value="__('Key Result (Optional)')" />
                <select id="key_result_id" name="key_result_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">No Key Result</option>
                    @foreach($objectives as $objective)
                        @foreach($objective->keyResults as $keyResult)
                            <option value="{{ $keyResult->id }}" {{ old('key_result_id') == $keyResult->id ? 'selected' : '' }}>
                                {{ $objective->title }} - {{ $keyResult->title }}
                            </option>
                        @endforeach
                    @endforeach
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('key_result_id')" />
            </div>

            <div class="mt-6">
                <x-input-label for="user_search" :value="__('Search Assignee')" />
                <input type="text" id="assignee_search" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Type to search users...">

                <x-input-label for="assignee_id" :value="__('Assign To')" class="mt-2" />
                <select id="assignee_id" name="assignee_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('assignee_id', auth()->id()) == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('assignee_id')" />
            </div>

            <div class="mt-6 grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="start_date" :value="__('Start Date')" />
                    <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" :value="old('start_date', date('Y-m-d'))" required min="{{ date('Y-m-d') }}" />
                    <x-input-error class="mt-2" :messages="$errors->get('start_date')" />
                </div>

                <div>
                    <x-input-label for="due_date" :value="__('Due Date')" />
                    <x-text-input id="due_date" name="due_date" type="date" class="mt-1 block w-full" :value="old('due_date')" required min="{{ date('Y-m-d') }}" />
                    <x-input-error class="mt-2" :messages="$errors->get('due_date')" />
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button @click="$dispatch('close-modal', 'create-task')" class="mr-3">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button>
                    {{ __('Create Task') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle user search
    const userSearch = document.getElementById('user_search');
    const userSelect = document.getElementById('user_id');
    
    if (userSearch && userSelect) {
        userSearch.addEventListener('input', function(e) {
            const searchValue = e.target.value.toLowerCase();
            Array.from(userSelect.options).forEach(option => {
                const matches = option.text.toLowerCase().includes(searchValue);
                option.style.display = matches ? '' : 'none';
            });
        });
    }

    // Handle user search for task assignee
    const assigneeSearch = document.getElementById('assignee_search');
    const assigneeSelect = document.getElementById('assignee_id');
    
    if (assigneeSearch && assigneeSelect) {
        assigneeSearch.addEventListener('input', function(e) {
            const searchValue = e.target.value.toLowerCase();
            Array.from(assigneeSelect.options).forEach(option => {
                const matches = option.text.toLowerCase().includes(searchValue);
                option.style.display = matches ? '' : 'none';
            });
        });
    }
});
</script>
@endpush
