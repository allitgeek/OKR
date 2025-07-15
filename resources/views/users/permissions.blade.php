<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Users') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Search Section -->
            <div class="mb-6">
                <div class="relative max-w-md">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input 
                        type="text" 
                        id="user-search" 
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm" 
                        placeholder="Search users by name or email..." 
                        autocomplete="off"
                    >
                </div>
                <div id="search-results" class="mt-2 text-sm text-gray-600">
                    <span id="results-text">Showing all {{ $users->count() }} users</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mb-4">
                <x-primary-button type="button" onclick="openModal('createUserModal')" class="bg-navy-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"/>
                    </svg>
                    <span class="sr-only">Create New User</span>
                </x-primary-button>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Desktop View -->
                    <div class="hidden md:block">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Name
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Email
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Current Roles
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($users as $user)
                                    <tr class="user-row" 
                                        data-name="{{ strtolower($user->name) }}" 
                                        data-email="{{ strtolower($user->email) }}"
                                        data-roles="{{ strtolower($user->roles->pluck('name')->implode(' ')) }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $user->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $user->email }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap gap-2">
                                                @foreach ($user->roles as $role)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-navy-100 text-navy-800">
                                                        {{ $role->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap gap-2">
                                                <x-primary-button type="button" onclick="openModal('edit-user-{{ $user->id }}')" title="Edit User">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                                    </svg>
                                                    <span class="sr-only">Edit User</span>
                                                </x-primary-button>
                                                <x-primary-button type="button" onclick="openModal('modal-{{ $user->id }}')" title="Edit Roles">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                                    </svg>
                                                    <span class="sr-only">Edit Roles</span>
                                                </x-primary-button>
                                                <form action="{{ route('users.permissions.super-admin', $user) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    <x-primary-button type="submit" class="bg-purple-600 hover:bg-purple-700" title="Make Super Admin">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                        </svg>
                                                        <span class="sr-only">Make Super Admin</span>
                                                    </x-primary-button>
                                                </form>
                                                @if($user->id !== auth()->id())
                                                    <form action="{{ route('users.delete', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <x-danger-button type="submit" title="Delete User">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                            </svg>
                                                            <span class="sr-only">Delete User</span>
                                                        </x-danger-button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile View -->
                    <div class="md:hidden space-y-4">
                        @foreach ($users as $user)
                            <div class="user-card bg-white rounded-lg shadow p-4 border border-gray-200"
                                 data-name="{{ strtolower($user->name) }}" 
                                 data-email="{{ strtolower($user->email) }}"
                                 data-roles="{{ strtolower($user->roles->pluck('name')->implode(' ')) }}">
                                <div class="space-y-2">
                                    <div>
                                        <label class="text-xs font-medium text-gray-500 uppercase">Name</label>
                                        <p class="mt-1">{{ $user->name }}</p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium text-gray-500 uppercase">Email</label>
                                        <p class="mt-1">{{ $user->email }}</p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium text-gray-500 uppercase">Roles</label>
                                        <div class="mt-1 flex flex-wrap gap-2">
                                            @foreach ($user->roles as $role)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-navy-100 text-navy-800">
                                                    {{ $role->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="pt-2 flex flex-wrap gap-2">
                                        <x-primary-button type="button" onclick="openModal('edit-user-{{ $user->id }}')" title="Edit User">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                            </svg>
                                            <span class="sr-only">Edit User</span>
                                        </x-primary-button>
                                        <x-primary-button type="button" onclick="openModal('modal-{{ $user->id }}')" title="Edit Roles">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                            </svg>
                                            <span class="sr-only">Edit Roles</span>
                                        </x-primary-button>
                                        <form action="{{ route('users.permissions.super-admin', $user) }}" method="POST" class="inline-block">
                                            @csrf
                                            <x-primary-button type="submit" class="bg-purple-600 hover:bg-purple-700" title="Make Super Admin">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                <span class="sr-only">Make Super Admin</span>
                                            </x-primary-button>
                                        </form>
                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('users.delete', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                @csrf
                                                @method('DELETE')
                                                <x-danger-button type="submit" title="Delete User">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                    </svg>
                                                    <span class="sr-only">Delete User</span>
                                                </x-danger-button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <x-user-modals :roles="$roles" />
    @foreach ($users as $user)
        <x-user-modals :user="$user" :roles="$roles" />
    @endforeach

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        // If there are any validation errors, show the create user modal
        @if($errors->any())
            openModal('createUserModal');
        @endif

        // Real-time user search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('user-search');
            const resultsText = document.getElementById('results-text');
            const userRows = document.querySelectorAll('.user-row');
            const userCards = document.querySelectorAll('.user-card');
            const allUsers = [...userRows, ...userCards];
            
            let searchTimeout;

            function performSearch() {
                const searchTerm = searchInput.value.toLowerCase().trim();
                let visibleCount = 0;

                allUsers.forEach(user => {
                    const name = user.dataset.name || '';
                    const email = user.dataset.email || '';
                    const roles = user.dataset.roles || '';
                    
                    const isMatch = name.includes(searchTerm) || 
                                  email.includes(searchTerm) || 
                                  roles.includes(searchTerm);

                    if (isMatch || searchTerm === '') {
                        user.style.display = '';
                        visibleCount++;
                    } else {
                        user.style.display = 'none';
                    }
                });

                // Update results text
                const totalUsers = {{ $users->count() }};
                if (searchTerm === '') {
                    resultsText.textContent = `Showing all ${totalUsers} users`;
                } else {
                    resultsText.textContent = `Found ${visibleCount} of ${totalUsers} users matching "${searchInput.value}"`;
                }

                // Add visual feedback for no results
                if (visibleCount === 0 && searchTerm !== '') {
                    resultsText.textContent = 'No users found matching your search';
                    resultsText.className = 'mt-2 text-sm text-red-600';
                } else {
                    resultsText.className = 'mt-2 text-sm text-gray-600';
                }
            }

            // Real-time search with debouncing
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(performSearch, 150); // 150ms delay for smooth UX
            });

            // Clear search on escape key
            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    searchInput.value = '';
                    performSearch();
                    searchInput.blur();
                }
            });

            // Initial search state
            performSearch();
        });
    </script>
</x-app-layout> 