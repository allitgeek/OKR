@php
    use Illuminate\Support\Str;
@endphp

<!-- Tasks List -->
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 text-gray-900">
        <h3 class="text-lg font-semibold mb-4">Tasks</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assignee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($tasks as $task)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $task->title }}</div>
                                <div class="text-sm text-gray-500">{{ Str::limit($task->description, 50) }}</div>
                                @if ($task->keyResult)
                                    <div class="text-xs text-gray-400 mt-1">
                                        {{ $task->keyResult->objective->title }} > {{ $task->keyResult->title }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $task->assignee->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($task->status === 'completed') bg-green-100 text-green-800
                                    @elseif($task->status === 'in_progress') bg-blue-100 text-blue-800
                                    @elseif($task->status === 'blocked') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $task->due_date ? $task->due_date->format('M d, Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('tasks.show', $task) }}" 
                                    class="text-blue-600 hover:text-blue-900 inline-flex items-center"
                                    title="View task details">
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