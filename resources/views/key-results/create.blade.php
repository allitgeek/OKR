<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Key Result') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('key-results.store') }}" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="objective_id" :value="__('Select Objective')" />
                            <select id="objective_id" name="objective_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Select an objective</option>
                                @foreach($objectives as $objective)
                                    <option value="{{ $objective->id }}" {{ old('objective_id') == $objective->id ? 'selected' : '' }}>
                                        {{ $objective->title }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('objective_id')" />
                        </div>

                        <div>
                            <x-input-label for="title" :value="__('Title')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="3" required>{{ old('description') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <div>
                            <x-input-label for="owner_id" :value="__('Assign To')" />
                            <select id="owner_id" name="owner_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Select an assignee</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('owner_id', auth()->id()) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('owner_id')" />
                        </div>

                        <div>
                            <x-input-label for="current_value" :value="__('Current Value')" />
                            <x-text-input id="current_value" name="current_value" type="number" step="0.01" class="mt-1 block w-full" :value="old('current_value', 0)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('current_value')" />
                        </div>

                        <div>
                            <x-input-label for="target_value" :value="__('Target Value')" />
                            <x-text-input id="target_value" name="target_value" type="number" step="0.01" class="mt-1 block w-full" :value="old('target_value', 100)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('target_value')" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
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

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Create Key Result') }}</x-primary-button>
                            <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const ownerSelect = document.getElementById('owner_id');
        
        if (ownerSelect) {
            // Store original options for filtering
            const originalOptions = Array.from(ownerSelect.options);
            
            // Add input event to filter options while typing with the select open
            ownerSelect.addEventListener('keyup', function(e) {
                const searchValue = e.target.value.toLowerCase();
                
                // Filter and update options based on search
                const filteredOptions = originalOptions.filter(option => 
                    option.text.toLowerCase().includes(searchValue)
                );
                
                // Clear current options
                ownerSelect.innerHTML = '';
                
                // Add filtered options back
                filteredOptions.forEach(option => ownerSelect.add(option.cloneNode(true)));
            });
            
            // Reset options when select is closed
            ownerSelect.addEventListener('blur', function() {
                setTimeout(() => {
                    ownerSelect.innerHTML = '';
                    originalOptions.forEach(option => ownerSelect.add(option.cloneNode(true)));
                }, 200);
            });
        }
    });
    </script>
    @endpush
</x-app-layout> 