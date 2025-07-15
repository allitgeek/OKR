<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800 leading-tight">
                {{ __('Objectives') }}
            </h2>
            <a href="{{ route('objectives.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-150 ease-in-out">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                {{ __('New Objective') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @forelse($objectives as $objective)
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg hover:shadow-md transition-shadow duration-200">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <span class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-600 font-semibold text-lg">
                                        {{ $loop->iteration }}
                                    </span>
                                    <h3 class="ml-3 text-xl font-semibold text-gray-900 truncate">
                                        <a href="{{ route('objectives.show', $objective) }}" class="hover:text-blue-600 transition-colors duration-150">
                                            {{ $objective->title }}
                                        </a>
                                    </h3>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('objectives.edit', $objective) }}" class="text-gray-400 hover:text-blue-600 transition-colors duration-150">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('objectives.destroy', $objective) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors duration-150" onclick="return confirm('Are you sure you want to delete this objective?')">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="flex items-center justify-between text-sm text-gray-600 mb-1">
                                    <span>Overall Progress</span>
                                    <span class="font-medium">{{ $objective->progress }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full transition-all duration-300 ease-in-out {{ $objective->progress >= 100 ? 'bg-green-500' : ($objective->progress >= 50 ? 'bg-blue-500' : 'bg-blue-500') }}" 
                                         style="width: {{ $objective->progress }}%">
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Key Results</span>
                                    <span class="font-medium text-gray-900">{{ $objective->keyResults->count() }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Time Period</span>
                                    <span class="font-medium text-gray-900">{{ ucfirst($objective->time_period) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Due Date</span>
                                    <span class="font-medium {{ $objective->end_date->isPast() ? 'text-red-600' : 'text-gray-900' }}">
                                        {{ $objective->end_date->format('M d, Y') }}
                                    </span>
                                </div>
                            </div>

                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <a href="{{ route('objectives.show', $objective) }}" 
                                   class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors duration-150">
                                    View Details
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full">
                        <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                            <div class="text-gray-500 mb-4">No objectives found</div>
                            <a href="{{ route('objectives.create') }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-150 ease-in-out">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Create Your First Objective
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            @if($objectives->hasPages())
                <div class="mt-6">
                    {{ $objectives->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout> 