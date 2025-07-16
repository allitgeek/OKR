<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    ✅ {{ __('Quick Check-in') }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Update your progress and confidence levels
                </p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('okr.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    🎯 Back to Dashboard
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">There were some errors:</h3>
                            <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white shadow rounded-lg">
                <form action="{{ route('okr-check-ins.store') }}" method="POST" class="p-6">
                    @csrf
                    
                    <!-- Check-in Type Selection -->
                    <div class="mb-6">
                        <label class="text-base font-medium text-gray-900">What would you like to check in on?</label>
                        <div class="mt-4 space-y-4">
                            <div class="flex items-center">
                                <input id="objective_check" name="check_type" type="radio" value="objective" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300" checked>
                                <label for="objective_check" class="ml-3 block text-sm font-medium text-gray-700">
                                    🎯 Objective Check-in
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input id="key_result_check" name="check_type" type="radio" value="key_result" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                                <label for="key_result_check" class="ml-3 block text-sm font-medium text-gray-700">
                                    📊 Key Result Check-in
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Objective Selection -->
                    <div id="objective_selection" class="mb-6">
                        <label for="objective_id" class="block text-sm font-medium text-gray-700">Select Objective</label>
                        <select id="objective_id" name="objective_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="">Choose an objective...</option>
                            @foreach($objectives ?? [] as $objective)
                                <option value="{{ $objective->id }}">{{ $objective->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Key Result Selection -->
                    <div id="key_result_selection" class="mb-6 hidden">
                        <label for="key_result_id" class="block text-sm font-medium text-gray-700">Select Key Result</label>
                        <select id="key_result_id" name="key_result_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="">Choose a key result...</option>
                            @foreach($keyResults ?? [] as $keyResult)
                                <option value="{{ $keyResult->id }}">{{ $keyResult->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Progress Update -->
                    <div class="mb-6">
                        <label for="progress_update" class="block text-sm font-medium text-gray-700">Progress Update</label>
                        <div class="mt-1">
                            <textarea id="progress_update" name="progress_update" rows="3" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md" placeholder="What progress have you made? What challenges are you facing?"></textarea>
                        </div>
                    </div>

                    <!-- Current Value (for Key Results) -->
                    <div id="current_value_section" class="mb-6 hidden">
                        <label for="current_value" class="block text-sm font-medium text-gray-700">Current Value</label>
                        <div class="mt-1">
                            <input type="number" step="0.01" id="current_value" name="current_value" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md" placeholder="Current progress value">
                        </div>
                    </div>

                    <!-- Confidence Level -->
                    <div class="mb-6">
                        <label for="confidence_level" class="block text-sm font-medium text-gray-700">Confidence Level</label>
                        <div class="mt-2">
                            <select id="confidence_level" name="confidence_level" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="0.9">🟢 High Confidence (90%)</option>
                                <option value="0.7">🟡 Medium-High Confidence (70%)</option>
                                <option value="0.5" selected>🟡 Medium Confidence (50%)</option>
                                <option value="0.3">🟠 Medium-Low Confidence (30%)</option>
                                <option value="0.1">🔴 Low Confidence (10%)</option>
                            </select>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">How confident are you about achieving this by the deadline?</p>
                    </div>

                    <!-- Risk Assessment -->
                    <div class="mb-6">
                        <label for="risk_level" class="block text-sm font-medium text-gray-700">Risk Level</label>
                        <div class="mt-2">
                            <select id="risk_level" name="risk_level" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="low">🟢 Low Risk</option>
                                <option value="medium" selected>🟡 Medium Risk</option>
                                <option value="high">🔴 High Risk</option>
                            </select>
                        </div>
                    </div>

                    <!-- Next Steps -->
                    <div class="mb-6">
                        <label for="next_steps" class="block text-sm font-medium text-gray-700">Next Steps</label>
                        <div class="mt-1">
                            <textarea id="next_steps" name="next_steps" rows="2" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md" placeholder="What are your next actions?"></textarea>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('okr.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            ✅ Submit Check-in
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Toggle between objective and key result selection
        document.addEventListener('DOMContentLoaded', function() {
            const objectiveRadio = document.getElementById('objective_check');
            const keyResultRadio = document.getElementById('key_result_check');
            const objectiveSection = document.getElementById('objective_selection');
            const keyResultSection = document.getElementById('key_result_selection');
            const currentValueSection = document.getElementById('current_value_section');

            function toggleSections() {
                if (objectiveRadio.checked) {
                    objectiveSection.classList.remove('hidden');
                    keyResultSection.classList.add('hidden');
                    currentValueSection.classList.add('hidden');
                } else {
                    objectiveSection.classList.add('hidden');
                    keyResultSection.classList.remove('hidden');
                    currentValueSection.classList.remove('hidden');
                }
            }

            objectiveRadio.addEventListener('change', toggleSections);
            keyResultRadio.addEventListener('change', toggleSections);
            
            // Initial setup
            toggleSections();
        });
    </script>
</x-app-layout> 