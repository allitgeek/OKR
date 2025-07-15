<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Add Key Result') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($objective->keyResults->isEmpty())
                        <div class="text-center py-4">
                            <p class="text-gray-600">No key results yet. Add your first key result below.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($objective->keyResults as $keyResult)
                                <div class="border rounded-lg p-4">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h4 class="font-medium">{{ $keyResult->title }}</h4>
                                            @if($keyResult->description)
                                                <p class="mt-1 text-sm text-gray-600">{{ $keyResult->description }}</p>
                                            @endif
                                            <div class="mt-2">
                                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $keyResult->progress }}%"></div>
                                                </div>
                                                <p class="mt-1 text-sm text-gray-600">
                                                    Progress: {{ $keyResult->progress }}%
                                                    ({{ $keyResult->current_value }} / {{ $keyResult->target_value }})
                                                </p>
                                                <div class="mt-3 flex space-x-2">
                                                    <button 
                                                        onclick="updateProgress('{{ $keyResult->id }}', 25)"
                                                        class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                                        title="Set progress to 25%">
                                                        <svg class="h-4 w-4 mr-1 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M15.24 4.76a2 2 0 0 1 0 2.83l-6.83 6.83a2 2 0 0 1-2.83 0l-3.17-3.17a2 2 0 0 1 2.83-2.83L8 11.17l5.66-5.66a2 2 0 0 1 2.83 0z"/>
                                                        </svg>
                                                        25%
                                                    </button>
                                                    <button 
                                                        onclick="updateProgress('{{ $keyResult->id }}', 50)"
                                                        class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                                        title="Set progress to 50%">
                                                        <svg class="h-4 w-4 mr-1 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M15.24 4.76a2 2 0 0 1 0 2.83l-6.83 6.83a2 2 0 0 1-2.83 0l-3.17-3.17a2 2 0 0 1 2.83-2.83L8 11.17l5.66-5.66a2 2 0 0 1 2.83 0z"/>
                                                        </svg>
                                                        50%
                                                    </button>
                                                    <button 
                                                        onclick="updateProgress('{{ $keyResult->id }}', 75)"
                                                        class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                                        title="Set progress to 75%">
                                                        <svg class="h-4 w-4 mr-1 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M15.24 4.76a2 2 0 0 1 0 2.83l-6.83 6.83a2 2 0 0 1-2.83 0l-3.17-3.17a2 2 0 0 1 2.83-2.83L8 11.17l5.66-5.66a2 2 0 0 1 2.83 0z"/>
                                                        </svg>
                                                        75%
                                                    </button>
                                                    <button 
                                                        onclick="updateProgress('{{ $keyResult->id }}', 100)"
                                                        class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                                        title="Set progress to 100%">
                                                        <svg class="h-4 w-4 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M15.24 4.76a2 2 0 0 1 0 2.83l-6.83 6.83a2 2 0 0 1-2.83 0l-3.17-3.17a2 2 0 0 1 2.83-2.83L8 11.17l5.66-5.66a2 2 0 0 1 2.83 0z"/>
                                                        </svg>
                                                        100%
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('key-results.edit', $keyResult) }}" 
                                               class="text-blue-600 hover:text-blue-900"
                                               title="Edit key result">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            <form action="{{ route('key-results.destroy', $keyResult) }}" 
                                                  method="POST" 
                                                  class="inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this key result?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-900"
                                                        title="Delete key result">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function updateProgress(keyResultId, progress) {
            if (confirm('Are you sure you want to update the progress to ' + progress + '%?')) {
                fetch(`/key-results/${keyResultId}/progress`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ progress })
                }).then(response => {
                    if (response.ok) {
                        window.location.reload();
                    } else {
                        alert('Failed to update progress. Please try again.');
                    }
                }).catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while updating progress.');
                });
            }
        }
    </script>
    @endpush
</x-app-layout> 