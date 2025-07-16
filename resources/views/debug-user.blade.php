<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Debug: Current User Info
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Current User Information</h3>
                    
                    @if(auth()->check())
                        <div class="space-y-2">
                            <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
                            <p><strong>Name:</strong> {{ auth()->user()->name }}</p>
                            <p><strong>User ID:</strong> {{ auth()->user()->id }}</p>
                            
                            <h4 class="font-semibold mt-4">Roles:</h4>
                            <ul class="list-disc list-inside">
                                @foreach(auth()->user()->roles as $role)
                                    <li>{{ $role->name }} ({{ $role->slug }})</li>
                                @endforeach
                            </ul>
                            
                            <h4 class="font-semibold mt-4">Key Permissions:</h4>
                            <ul class="list-disc list-inside">
                                <li>view-all-objectives: {{ auth()->user()->hasPermission('view-all-objectives') ? 'YES' : 'NO' }}</li>
                                <li>manage-objectives: {{ auth()->user()->hasPermission('manage-objectives') ? 'YES' : 'NO' }}</li>
                                <li>manage-key-results: {{ auth()->user()->hasPermission('manage-key-results') ? 'YES' : 'NO' }}</li>
                            </ul>
                            
                            <h4 class="font-semibold mt-4">Key Result 27 Check:</h4>
                            @php
                                $keyResult = \App\Models\KeyResult::find(27);
                            @endphp
                            @if($keyResult)
                                <ul class="list-disc list-inside">
                                    <li>Key Result Owner ID: {{ $keyResult->owner_id }}</li>
                                    <li>Objective Owner ID: {{ $keyResult->objective->user_id }}</li>
                                    <li>Can edit: {{ auth()->user()->can('update', $keyResult) ? 'YES' : 'NO' }}</li>
                                </ul>
                            @else
                                <p>Key Result 27 not found</p>
                            @endif
                        </div>
                    @else
                        <p class="text-red-600">❌ No user is logged in!</p>
                    @endif
                    
                    <div class="mt-6">
                        <a href="{{ route('dashboard') }}" class="bg-blue-500 text-white px-4 py-2 rounded">Back to Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 