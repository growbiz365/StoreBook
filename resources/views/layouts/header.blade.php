<div class="lg:pl-72">
    <div
        class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
        <button type="button" class="-m-2.5 p-2.5 text-gray-700 lg:hidden" @click="sidebarOpen = true">
            <span class="sr-only">Open sidebar</span>
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>

        <!-- Separator -->
        <div class="h-6 w-px bg-gray-200 lg:hidden" aria-hidden="true"></div>
        <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
            {{-- Search form commented out --}}
            <div class="flex-1"></div>

            <div x-data="{ branchMenuOpen: false }" class="relative flex items-center">
                <!-- Button to show current branch -->
                @if(auth()->user()->businesses->isNotEmpty()) <!-- Check if the user has any branches -->
                    <button type="button"
                        class="flex items-center justify-between text-gray-900 px-3 py-2 text-sm font-medium bg-white hover:bg-purple-50 border border-gray-200 hover:border-purple-300 rounded-lg shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-1 group"
                        @click="branchMenuOpen = !branchMenuOpen"
                        :class="{ 'ring-2 ring-purple-500 border-purple-300 bg-purple-50': branchMenuOpen }">

                        <div class="flex items-center min-w-0">
                            <!-- Business Icon with live indicator -->
                            <div class="flex-shrink-0 mr-3 relative">
                                <div class="h-8 w-8 bg-gradient-to-br from-purple-600 via-purple-700 to-teal-600 rounded-lg flex items-center justify-center shadow-md">
                                    <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <!-- Live indicator with animation -->
                                <div class="absolute -top-1 -right-1">
                                    <div class="relative">
                                        <div class="h-3 w-3 bg-emerald-400 rounded-full border-2 border-white shadow-sm"></div>
                                        <div class="absolute inset-0 h-3 w-3 bg-emerald-400 rounded-full animate-ping opacity-75"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Business Info -->
                            <div class="min-w-0 flex-1">
                                @if (session('active_business'))
                                    @php
                                        $activeBusiness = \App\Models\Business::find(session('active_business'));
                @endphp
                                    <div class="text-sm font-semibold text-gray-900 truncate max-w-40 group-hover:text-purple-700 transition-colors duration-200">
                                        {{ $activeBusiness->business_name }}
                                    </div>
                                    <div class="text-xs text-gray-500 mt-0.5">
                                        {{ $activeBusiness->package->package_name }}
                                    </div>
                            @else
                                    <div class="text-sm font-medium text-gray-700">
                                        Select Business
                                    </div>
                                    <div class="text-xs text-gray-500 mt-0.5">
                                        No active business
                                    </div>
                            @endif
                            </div>
                        </div>

                        <!-- Chevron Icon with enhanced animation -->
                        <div class="flex-shrink-0 ml-3">
                            <svg class="h-5 w-5 text-gray-400 transform transition-all duration-300 group-hover:text-purple-500"
                                :class="{ 'rotate-180 text-purple-600': branchMenuOpen }"
                                viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/>
                        </svg>
                        </div>
                    </button>

                    <!-- Enhanced Modern Dropdown -->
                    <div x-show="branchMenuOpen"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                        @click.away="branchMenuOpen = false"
                        class="absolute right-0 z-50 top-full mt-3 w-80 bg-white rounded-xl shadow-2xl ring-1 ring-black ring-opacity-5 focus:outline-none border border-gray-100 overflow-hidden backdrop-blur-sm">

                        <!-- Modern Header with gradient -->
                        <div class="bg-gradient-to-r from-purple-50 via-purple-100 to-teal-50 px-4 py-3 border-b border-gray-100">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <div class="h-6 w-6 bg-gradient-to-br from-purple-600 to-teal-600 rounded-lg flex items-center justify-center shadow-sm">
                                        <svg class="h-3 w-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-semibold text-gray-800">Switch Business</h3>
                                </div>
                                <div class="flex items-center space-x-1">
                                    <div class="h-2 w-2 bg-emerald-400 rounded-full animate-pulse"></div>
                                    <span class="text-xs font-medium text-emerald-600">Live</span>
                                </div>
                            </div>
                        </div>

                        <!-- Businesses List with modern styling -->
                        <div class="max-h-64 overflow-y-auto py-2">
                            @foreach (auth()->user()->businesses as $business)
                            <a href="{{ route('businesses.activate', $business->id) }}"
                                    class="group flex items-center px-4 py-3 hover:bg-gradient-to-r hover:from-purple-50 hover:to-teal-50 transition-all duration-200 border-l-4 border-transparent hover:border-purple-400 {{ session('active_business') == $business->id ? 'bg-gradient-to-r from-purple-50 to-teal-50 border-purple-500' : '' }}">

                                    <!-- Enhanced Business Icon -->
                                    <div class="flex-shrink-0 mr-4 relative">
                                        <div class="h-10 w-10 bg-gradient-to-br {{ session('active_business') == $business->id ? 'from-purple-600 via-purple-700 to-teal-600' : 'from-gray-400 to-gray-500 group-hover:from-purple-500 group-hover:to-teal-500' }} rounded-xl flex items-center justify-center transition-all duration-300 shadow-lg group-hover:shadow-xl transform group-hover:scale-105">
                                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                        </div>
                                        @if(session('active_business') == $business->id)
                                            <!-- Active indicator with animation -->
                                            <div class="absolute -top-1 -right-1">
                                                <div class="relative">
                                                    <div class="h-4 w-4 bg-emerald-400 rounded-full border-2 border-white shadow-sm flex items-center justify-center">
                                                        <svg class="h-2 w-2 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                        </svg>
                                                    </div>
                                                    <div class="absolute inset-0 h-4 w-4 bg-emerald-400 rounded-full animate-ping opacity-75"></div>
                                                </div>
                                            </div>
                                        @else
                                            <!-- Inactive indicator -->
                                            <div class="absolute -top-1 -right-1">
                                                <div class="h-3 w-3 bg-gray-300 rounded-full border-2 border-white group-hover:bg-purple-400 transition-colors duration-200"></div>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Enhanced Business Details -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <div class="text-sm font-semibold text-gray-900 group-hover:text-purple-700 transition-colors duration-200 truncate pr-2">
                                                {{ $business->business_name }}
                                            </div>
                                            @if(session('active_business') == $business->id)
                                                <div class="flex-shrink-0">
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                        <div class="h-1.5 w-1.5 bg-emerald-400 rounded-full mr-1 animate-pulse"></div>
                                                        Active
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex items-center justify-between mt-1">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-gradient-to-r from-purple-100 to-teal-100 text-purple-800 border border-purple-200">
                                                <svg class="h-3 w-3 mr-1 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                {{ $business->package->package_name }}
                                            </span>
                                        </div>
                                    </div>
                            </a>
                        @endforeach
                        </div>

                        <!-- Modern Footer -->
                        <div class="bg-gray-50 px-4 py-2 border-t border-gray-100">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">{{ auth()->user()->businesses->count() }} business{{ auth()->user()->businesses->count() !== 1 ? 'es' : '' }} available</span>
                                <div class="flex items-center space-x-1">
                                    <div class="h-1.5 w-1.5 bg-emerald-400 rounded-full animate-pulse"></div>
                                    <span class="text-xs text-gray-500">Live sync</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>



            <div class="flex items-center gap-x-4 lg:gap-x-6">
                {{-- <button type="button" class="-m-2.5 p-2.5 text-gray-400 hover:text-gray-500">
                    <span class="sr-only">View notifications</span>
                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                        aria-hidden="true" data-slot="icon">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                    </svg>
                </button> --}}
                <!-- Separator -->
                <div class="hidden lg:block lg:h-6 lg:w-px lg:bg-gray-200" aria-hidden="true"></div>
                <!-- Enhanced Profile dropdown -->
                <div class="relative" x-data="{ profileMenuOpen: false }">
                    <button type="button" 
                        class="group -m-1.5 flex items-center p-1.5 hover:bg-purple-50 rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2"
                        @click="profileMenuOpen = !profileMenuOpen"
                        :class="{ 'bg-purple-50 ring-2 ring-purple-400': profileMenuOpen }">
                        <span class="sr-only">Open user menu</span>
                        
                        <!-- Enhanced Avatar with status -->
                        <div class="relative">
                            <div class="h-9 w-9 rounded-full bg-gradient-to-br from-purple-600 via-purple-700 to-teal-600 flex items-center justify-center ring-2 ring-white shadow-md group-hover:shadow-lg transition-all duration-200 group-hover:scale-105">
                                <span class="text-sm font-bold text-white uppercase tracking-tight">
                                    {{ substr(Auth::user()->name, 0, 2) }}
                                </span>
                            </div>
                            <!-- Online status indicator with pulse -->
                            <div class="absolute -bottom-0.5 -right-0.5">
                                <div class="relative">
                                    <div class="h-3 w-3 bg-emerald-500 border-2 border-white rounded-full"></div>
                                    <div class="absolute inset-0 h-3 w-3 bg-emerald-400 rounded-full animate-ping opacity-75"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- User info with role -->
                        <span class="hidden lg:flex lg:items-center lg:ml-3">
                            <span class="flex flex-col items-start">
                                <span class="text-sm font-semibold text-gray-900 group-hover:text-purple-700 transition-colors duration-200">
                                    {{ Auth::user()->name }}
                                </span>
                                <span class="text-xs text-gray-500 font-medium">
                                    {{ Auth::user()->email }}
                                </span>
                            </span>
                            <svg class="ml-3 h-5 w-5 text-gray-400 transform transition-all duration-200 group-hover:text-purple-600"
                                :class="{'rotate-180 text-purple-600': profileMenuOpen}"
                                viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z"
                                    clip-rule="evenodd" />
                            </svg>
                        </span>
                    </button>
                    
                    <!-- Enhanced Dropdown Menu -->
                    <div x-show="profileMenuOpen" 
                        x-transition:enter="transition ease-out duration-200 transform"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150 transform"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                        @click.away="profileMenuOpen = false"
                        class="absolute right-0 z-50 mt-3 w-72 origin-top-right rounded-xl bg-white shadow-2xl ring-1 ring-black/5 border border-gray-100 overflow-hidden"
                        role="menu" aria-orientation="vertical">
                        
                        <!-- Enhanced User Info Header -->
                        <div class="px-4 py-4 bg-gradient-to-br from-purple-50 via-purple-100 to-teal-50 border-b border-gray-200">
                            <div class="flex items-center space-x-3">
                                <div class="relative flex-shrink-0">
                                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-purple-600 via-purple-700 to-teal-600 flex items-center justify-center ring-2 ring-white shadow-lg">
                                        <span class="text-base font-bold text-white uppercase tracking-tight">
                                            {{ substr(Auth::user()->name, 0, 2) }}
                                        </span>
                                    </div>
                                    <!-- Status indicator -->
                                    <div class="absolute -bottom-1 -right-1">
                                        <div class="relative">
                                            <div class="h-4 w-4 bg-emerald-500 border-2 border-white rounded-full flex items-center justify-center">
                                                <div class="h-1.5 w-1.5 bg-white rounded-full"></div>
                                            </div>
                                            <div class="absolute inset-0 h-4 w-4 bg-emerald-400 rounded-full animate-ping opacity-75"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-600 truncate font-medium">{{ Auth::user()->email }}</p>
                                    <div class="flex items-center mt-1.5 space-x-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            <div class="h-1.5 w-1.5 bg-emerald-500 rounded-full mr-1 animate-pulse"></div>
                                            Online
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Menu Items with enhanced styling -->
                        <div class="py-2">
                            <a href="{{ route('profile.edit') }}"
                                class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 hover:bg-purple-50 hover:text-purple-900 transition-all duration-200 border-l-4 border-transparent hover:border-purple-600"
                                role="menuitem">
                                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-purple-100 group-hover:bg-purple-200 transition-colors duration-200 mr-3">
                                    <svg class="h-4 w-4 text-purple-600 group-hover:text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="font-semibold">Your Profile</div>
                                    <div class="text-xs text-gray-500">Manage your account</div>
                                </div>
                                <svg class="h-4 w-4 text-gray-400 group-hover:text-purple-600 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>

                            <a href="{{ route('settings') }}"
                                class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 hover:bg-teal-50 hover:text-teal-900 transition-all duration-200 border-l-4 border-transparent hover:border-teal-600"
                                role="menuitem">
                                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-teal-100 group-hover:bg-teal-200 transition-colors duration-200 mr-3">
                                    <svg class="h-4 w-4 text-teal-600 group-hover:text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="font-semibold">Settings</div>
                                    <div class="text-xs text-gray-500">Preferences & privacy</div>
                                </div>
                                <svg class="h-4 w-4 text-gray-400 group-hover:text-teal-600 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>

                            

                            <!-- Divider -->
                            <div class="my-2 border-t border-gray-200"></div>

                            <!-- Sign Out -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a href="{{ route('logout') }}"
                                    class="group flex items-center px-4 py-3 text-sm font-medium text-red-600 hover:bg-red-50 hover:text-red-700 transition-all duration-200 border-l-4 border-transparent hover:border-red-500"
                                    role="menuitem"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-red-100 group-hover:bg-red-200 transition-colors duration-200 mr-3">
                                        <svg class="h-4 w-4 text-red-600 group-hover:text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-semibold">Sign Out</div>
                                        <div class="text-xs text-red-500">End your session</div>
                                    </div>
                                    <svg class="h-4 w-4 text-red-400 group-hover:text-red-600 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </form>
                        </div>

                        <!-- Footer with timestamp -->
                        <div class="px-4 py-2.5 bg-gray-50 border-t border-gray-200">
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span class="flex items-center space-x-1">
                                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                    </svg>
                                    <span>{{ now()->setTimezone($businessTimezone ?? 'Asia/Karachi')->format('g:i A') }}</span>
                                </span>
                                @if(session('active_business') && isset($activeBusiness))
                                    <span class="font-medium text-purple-600">{{ $activeBusiness->business_name }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
