@php
    use Illuminate\Support\Facades\Auth;
@endphp

<!-- Primary Navigation Menu -->
<nav x-data="{ open: false }" class="bg-gradient-to-r from-indigo-900 via-blue-900 to-purple-900 shadow-xl border-b border-indigo-800/30 mobile-nav">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <!-- Logo and Mobile Hamburger -->
            <div class="flex items-center justify-between w-full">
                <!-- Enhanced Logo -->
                <div class="shrink-0 flex items-center group">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 transition-transform duration-200 hover:scale-105">
                        <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow-lg touch-target-sm">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div class="hidden sm:block">
                            <div class="text-xl font-bold text-white tracking-tight">OKR</div>
                            <div class="text-xs text-indigo-200 -mt-1">Management System</div>
                        </div>
                    </a>
                </div>

                <!-- Mobile Menu Toggle -->
                <div class="sm:hidden flex items-center">
                    <button 
                        @click="open = !open" 
                        class="inline-flex items-center justify-center p-2 rounded-md text-indigo-200 hover:text-white hover:bg-white/10 focus:outline-none transition touch-target-md"
                        aria-label="Toggle mobile menu"
                    >
                        <svg x-show="!open" class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        <svg x-show="open" x-cloak class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden sm:flex sm:items-center sm:ml-6 space-x-4">
                <!-- Enhanced Navigation Links -->
                <div class="hidden space-x-1 sm:-my-px sm:ml-8 sm:flex">
                    <!-- Dashboard -->
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" 
                        class="group flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-white/10 text-white shadow-lg' : 'text-indigo-100 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                        </svg>
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <!-- OKR Dashboard -->
                    <x-nav-link :href="route('okr.dashboard')" :active="request()->routeIs('okr.*')" 
                        class="group flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('okr.*') ? 'bg-white/10 text-white shadow-lg' : 'text-indigo-100 hover:text-white hover:bg-white/5' }}">
                        <div class="w-4 h-4 mr-2 text-lg">🎯</div>
                        {{ __('OKR Dashboard') }}
                        <span class="ml-2 px-2 py-0.5 text-xs bg-yellow-400 text-yellow-900 rounded-full font-semibold">NEW</span>
                    </x-nav-link>

                    <!-- Objectives -->
                    <x-nav-link :href="route('objectives.index')" :active="request()->routeIs('objectives.*')" 
                        class="group flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('objectives.*') ? 'bg-white/10 text-white shadow-lg' : 'text-indigo-100 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ __('Objectives') }}
                        @php 
                            try {
                                $objectivesCount = auth()->user() && auth()->user()->company_id 
                                    ? \App\Models\Objective::where('company_id', auth()->user()->company_id)->count() 
                                    : 0;
                            } catch (\Exception $e) {
                                $objectivesCount = 0;
                            }
                        @endphp
                        @if($objectivesCount > 0)
                            <span class="ml-2 px-2 py-0.5 text-xs bg-indigo-500 text-white rounded-full">{{ $objectivesCount }}</span>
                        @endif
                    </x-nav-link>

                    <!-- Tasks -->
                    <x-nav-link :href="route('tasks.index')" :active="request()->routeIs('tasks.*')" 
                        class="group flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('tasks.*') ? 'bg-white/10 text-white shadow-lg' : 'text-indigo-100 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        {{ __('Tasks') }}
                        @php 
                            try {
                                $pendingTasks = \App\Models\Task::where('status', '!=', 'completed')->count();
                            } catch (\Exception $e) {
                                $pendingTasks = 0;
                            }
                        @endphp
                        @if($pendingTasks > 0)
                            <span class="ml-2 px-2 py-0.5 text-xs bg-orange-500 text-white rounded-full">{{ $pendingTasks }}</span>
                        @endif
                    </x-nav-link>

                    <!-- Analytics -->
                    @can('view-analytics')
                    <x-nav-link :href="route('analytics.dashboard')" :active="request()->routeIs('analytics.*')" 
                        class="group flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('analytics.*') ? 'bg-white/10 text-white shadow-lg' : 'text-indigo-100 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        {{ __('Analytics') }}
                    </x-nav-link>
                    @endcan
                </div>
            </div>

            <!-- Right Side Navigation -->
            <div class="flex items-center space-x-4">


                <!-- Settings Dropdown (Super Admin Only) -->
                @if(Auth::user()->hasRole('super-admin'))
                <div class="hidden sm:flex sm:items-center">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium text-indigo-100 hover:text-white hover:bg-white/10 focus:outline-none transition-all duration-200">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Settings
                                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="px-4 py-2 text-xs text-gray-500 border-b">
                                System Administration
                            </div>
                            <x-dropdown-link :href="route('users.permissions.index')" class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                                {{ __('Users') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('teams.index')" class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                {{ __('Teams') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('companies.create')" class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                {{ __('Company') }}
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>
                </div>
                @endif

                <!-- Help Dropdown -->
                <div class="hidden sm:flex sm:items-center">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium text-indigo-100 hover:text-white hover:bg-white/10 focus:outline-none transition-all duration-200">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Help
                                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('how-to.index')" class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                                {{ __('How to use OKR') }}
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>
                </div>

                <!-- User Profile Dropdown -->
                <div class="hidden sm:flex sm:items-center">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium text-white hover:bg-white/10 focus:outline-none transition-all duration-200">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span class="text-sm font-medium">{{ Auth::user()->name }}</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="px-4 py-2 text-xs text-gray-500 border-b">
                                {{ Auth::user()->email }}
                            </div>
                            <x-dropdown-link :href="route('profile.edit')" class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <div class="border-t border-gray-100"></div>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();" 
                                        class="flex items-center text-red-600 hover:bg-red-50">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>
        </div>

        <!-- Enhanced Responsive Navigation Menu -->
        <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-gradient-to-b from-indigo-900 to-purple-900 border-t border-indigo-800/30">
            <div class="px-4 pt-4 pb-3 space-y-2">
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" 
                    class="flex items-center px-3 py-2 rounded-lg text-indigo-100 hover:text-white hover:bg-white/10 transition-all duration-200">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                    </svg>
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
                
                <x-responsive-nav-link :href="route('okr.dashboard')" :active="request()->routeIs('okr.*')" 
                    class="flex items-center px-3 py-2 rounded-lg text-indigo-100 hover:text-white hover:bg-white/10 transition-all duration-200">
                    <div class="w-4 h-4 mr-3 text-lg">🎯</div>
                    {{ __('OKR Dashboard') }}
                </x-responsive-nav-link>
                
                <x-responsive-nav-link :href="route('objectives.index')" :active="request()->routeIs('objectives.*')" 
                    class="flex items-center px-3 py-2 rounded-lg text-indigo-100 hover:text-white hover:bg-white/10 transition-all duration-200">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ __('Objectives') }}
                </x-responsive-nav-link>
                
                <x-responsive-nav-link :href="route('tasks.index')" :active="request()->routeIs('tasks.*')" 
                    class="flex items-center px-3 py-2 rounded-lg text-indigo-100 hover:text-white hover:bg-white/10 transition-all duration-200">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    {{ __('Tasks') }}
                </x-responsive-nav-link>
                
                @can('view-analytics')
                <x-responsive-nav-link :href="route('analytics.dashboard')" :active="request()->routeIs('analytics.*')" 
                    class="flex items-center px-3 py-2 rounded-lg text-indigo-100 hover:text-white hover:bg-white/10 transition-all duration-200">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    {{ __('Analytics') }}
                </x-responsive-nav-link>
                @endcan
                
                <x-responsive-nav-link :href="route('how-to.index')" :active="request()->routeIs('how-to.*')" 
                    class="flex items-center px-3 py-2 rounded-lg text-indigo-100 hover:text-white hover:bg-white/10 transition-all duration-200">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ __('How to use OKR') }}
                </x-responsive-nav-link>
                
                @if(Auth::user()->hasRole('super-admin'))
                @can('manage-users')
                <x-responsive-nav-link :href="route('users.permissions.index')" :active="request()->routeIs('users.permissions.*')" 
                    class="flex items-center px-3 py-2 rounded-lg text-indigo-100 hover:text-white hover:bg-white/10 transition-all duration-200">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                    {{ __('Users') }}
                </x-responsive-nav-link>
                @endcan
                @endif
            </div>

            <!-- Enhanced Responsive Settings Options -->
            <div class="pt-4 pb-3 border-t border-indigo-800/30 bg-gradient-to-b from-purple-900 to-indigo-900">
                <div class="px-4 flex items-center space-x-3">
                    <svg class="w-8 h-8 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <div class="flex-1">
                        <div class="text-base font-medium text-white">{{ Auth::user()->name }}</div>
                        <div class="text-sm text-indigo-200">{{ Auth::user()->email }}</div>
                    </div>
                </div>

                <div class="mt-3 px-4 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')" 
                        class="flex items-center px-3 py-2 rounded-lg text-indigo-100 hover:text-white hover:bg-white/10 transition-all duration-200">
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();" 
                                class="flex items-center px-3 py-2 rounded-lg text-red-300 hover:text-red-100 hover:bg-red-900/20 transition-all duration-200">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Add mobile-specific styles -->
@push('styles')
<style>
    .mobile-nav-link {
        @apply block px-4 py-3 text-sm text-white hover:bg-white/10 transition-colors duration-200 flex items-center;
    }

    .mobile-action-button {
        @apply w-full flex items-center justify-center px-4 py-3 text-white rounded-lg shadow-md transition-all duration-200 touch-target-md;
    }

    /* Ensure touch targets are large enough on mobile */
    @media (max-width: 640px) {
        .touch-target-sm, .touch-target-md {
            min-width: 44px;
            min-height: 44px;
        }
    }
</style>
@endpush
