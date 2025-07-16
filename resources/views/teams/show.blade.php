<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $team->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Team Details</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ $team->description }}
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h4 class="text-md font-medium text-gray-900">Leader</h4>
                            <p class="mt-1 text-sm text-gray-600">{{ $team->leader->name }}</p>
                        </div>
                        <div>
                            <h4 class="text-md font-medium text-gray-900">Parent Team</h4>
                            <p class="mt-1 text-sm text-gray-600">{{ $team->parentTeam ? $team->parentTeam->name : 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900">Team Members</h3>
                        <ul class="mt-2 divide-y divide-gray-200">
                            @foreach ($team->members as $member)
                                <li class="py-2 flex items-center justify-between">
                                    <span class="text-sm text-gray-600">{{ $member->name }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('teams.index') }}" class="text-indigo-600 hover:text-indigo-900">Back to Teams</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 