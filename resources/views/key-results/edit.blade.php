<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Key Result') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('key-results.update', $keyResult) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        @if(session('error'))
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                                <span class="block sm:inline">{{ session('error') }}</span>
                            </div>
                        @endif

                        @if(session('success'))
                            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                                <span class="block sm:inline">{{ session('success') }}</span>
                            </div>
                        @endif

                        <div>
                            <x-input-label for="title" :value="__('Title')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $keyResult->title)" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $keyResult->description) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="start_date" :value="__('Start Date')" />
                                <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" :value="old('start_date', $keyResult->start_date?->format('Y-m-d'))" required />
                                <x-input-error class="mt-2" :messages="$errors->get('start_date')" />
                            </div>

                            <div>
                                <x-input-label for="due_date" :value="__('Due Date')" />
                                <x-text-input id="due_date" name="due_date" type="date" class="mt-1 block w-full" :value="old('due_date', $keyResult->due_date?->format('Y-m-d'))" required />
                                <x-input-error class="mt-2" :messages="$errors->get('due_date')" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="target_value" :value="__('Target Value')" />
                                <x-text-input id="target_value" name="target_value" type="number" step="0.01" class="mt-1 block w-full" :value="old('target_value', $keyResult->target_value)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('target_value')" />
                            </div>

                            <div>
                                <x-input-label for="current_value" :value="__('Current Value')" />
                                <x-text-input id="current_value" name="current_value" type="number" step="0.01" class="mt-1 block w-full" :value="old('current_value', $keyResult->current_value)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('current_value')" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="weight" :value="__('Weight')" />
                            <x-text-input id="weight" name="weight" type="number" step="0.1" class="mt-1 block w-full" :value="old('weight', $keyResult->weight)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('weight')" />
                        </div>

                        <div>
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="in_progress" @selected($keyResult->status === 'in_progress')>{{ __('In Progress') }}</option>
                                <option value="completed" @selected($keyResult->status === 'completed')>{{ __('Completed') }}</option>
                                <option value="not_started" @selected($keyResult->status === 'not_started')>{{ __('Not Started') }}</option>
                                <option value="on_hold" @selected($keyResult->status === 'on_hold')>{{ __('On Hold') }}</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <!-- Assignee -->
                        <div class="mt-4">
                            <x-input-label for="assignee_id" :value="__('Assignee')" />
                            <select id="assignee_id" name="assignee_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">{{ __('Unassigned') }}</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" @selected($keyResult->assignee_id == $user->id)>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('assignee_id')" class="mt-2" />
                        </div>


                        <input type="hidden" name="objective_id" value="{{ $keyResult->objective_id }}" />

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Save') }}</x-primary-button>
                            <a href="{{ route('objectives.show', $keyResult->objective_id) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 