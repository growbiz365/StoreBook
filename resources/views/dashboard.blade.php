<x-app-layout>
    @section('title', 'Dashboard - StoreBook')
    
    <!-- Subtle Background Gradient -->
    <div class="fixed inset-0 -z-10 bg-gradient-to-br from-purple-50/30 via-white to-teal-50/20 pointer-events-none"></div>
    
    <!-- Header Section - Enhanced Glass Morphism -->
    <div class="relative backdrop-blur-xl bg-white/70 rounded-2xl shadow-xl shadow-purple-500/5 border border-white/60 p-6 lg:p-8 mb-8 mt-4 overflow-hidden group">
        <!-- Animated Background Orbs -->
        <div class="absolute -top-24 -right-24 w-64 h-64 bg-gradient-to-br from-purple-400/20 to-teal-400/20 rounded-full blur-3xl group-hover:scale-110 transition-transform duration-700"></div>
        <div class="absolute -bottom-16 -left-16 w-48 h-48 bg-gradient-to-tr from-purple-400/15 to-indigo-400/15 rounded-full blur-2xl group-hover:scale-110 transition-transform duration-700"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-32 h-32 bg-gradient-to-br from-teal-300/10 to-purple-300/10 rounded-full blur-2xl"></div>

        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <!-- Left Section - Welcome Content -->
            <div class="flex items-start lg:items-center gap-4 lg:gap-5 flex-1">
                <!-- Premium Icon with Glow -->
                <div class="relative flex-shrink-0">
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-600 to-teal-600 rounded-2xl blur-xl opacity-60 group-hover:opacity-80 transition-opacity duration-300"></div>
                    <div class="relative bg-gradient-to-br from-purple-600 via-purple-700 to-teal-600 p-3.5 lg:p-4 rounded-2xl shadow-xl transform group-hover:scale-105 transition-all duration-300">
                        <svg class="h-6 w-6 lg:h-7 lg:w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
                
                <!-- Welcome Text -->
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl lg:text-3xl font-bold bg-gradient-to-r from-gray-900 via-purple-800 to-teal-800 bg-clip-text text-transparent mb-2 leading-tight">
                        Welcome back, <span class="bg-gradient-to-r from-purple-700 to-teal-700 bg-clip-text text-transparent">{{ Auth::user()->name }}</span>!
                    </h1>
                    <p class="text-sm lg:text-base text-gray-600 mb-3">Here's what's happening with your business today</p>
                    
                    <!-- System Status -->
                    <div class="flex items-center gap-2.5">
                        <div class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                        </div>
                        <span class="text-xs lg:text-sm font-medium text-gray-700">All systems operational</span>
                    </div>
                </div>
            </div>

            <!-- Right Section - Premium Time Display -->
            <div class="flex items-center gap-4 lg:gap-5 flex-shrink-0">
                <!-- Date Display -->
                <div class="text-right space-y-1 hidden sm:block">
                    <div class="text-sm lg:text-base font-bold text-gray-900" id="current-day">{{ now()->setTimezone($businessTimezone ?? 'Asia/Karachi')->format('l') }}</div>
                    <div class="text-xs lg:text-sm text-gray-500 font-medium" id="current-date">{{ now()->setTimezone($businessTimezone ?? 'Asia/Karachi')->format('M d, Y') }}</div>
                </div>
                
                <!-- Separator -->
                <div class="hidden sm:block h-12 w-px bg-gradient-to-b from-transparent via-gray-300 to-transparent"></div>
                
                <!-- Enhanced Watch Display -->
                <div class="relative backdrop-blur-sm bg-gradient-to-br from-white/90 via-purple-50/90 to-teal-50/90 rounded-2xl px-4 py-3 lg:px-5 lg:py-3.5 border border-white/60 shadow-xl hover:shadow-2xl transition-all duration-300 group/time overflow-hidden">
                    <!-- Animated Background Glow -->
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-500/20 via-teal-500/20 to-purple-500/20 opacity-0 group-hover/time:opacity-100 transition-opacity duration-500"></div>
                    <div class="absolute -top-10 -right-10 w-24 h-24 bg-purple-400/30 rounded-full blur-2xl group-hover/time:scale-150 transition-transform duration-700"></div>
                    <div class="absolute -bottom-10 -left-10 w-20 h-20 bg-teal-400/30 rounded-full blur-2xl group-hover/time:scale-150 transition-transform duration-700"></div>
                    
                    <div class="relative flex items-center gap-3 lg:gap-4">
                        <!-- Watch Face -->
                        <div class="relative flex-shrink-0">
                            <!-- Outer Glow Ring -->
                            <div class="absolute inset-0 bg-gradient-to-br from-purple-600 via-purple-700 to-teal-600 rounded-full blur-md opacity-50 group-hover/time:opacity-70 transition-opacity duration-300"></div>
                            
                            <!-- Watch Case -->
                            <div class="relative w-10 h-10 lg:w-12 lg:h-12 bg-gradient-to-br from-slate-800 via-gray-800 to-slate-900 rounded-full p-0.5 shadow-2xl ring-1 ring-white/30">
                                <!-- Inner Bezel -->
                                <div class="w-full h-full bg-gradient-to-br from-gray-900 to-gray-800 rounded-full p-1 relative overflow-hidden">
                                    <!-- Watch Face Background -->
                                    <div class="w-full h-full bg-white rounded-full relative">
                                        <!-- All 12 Hour Markers using SVG for precise positioning -->
                                        <svg class="absolute inset-0 w-full h-full" viewBox="0 0 100 100">
                                            <!-- 12 major hour markers (every 3 hours) -->
                                            <line x1="50" y1="8" x2="50" y2="12" stroke="#1f2937" stroke-width="1.5" stroke-linecap="round"/>
                                            <line x1="92" y1="50" x2="88" y2="50" stroke="#1f2937" stroke-width="1.5" stroke-linecap="round"/>
                                            <line x1="50" y1="92" x2="50" y2="88" stroke="#1f2937" stroke-width="1.5" stroke-linecap="round"/>
                                            <line x1="8" y1="50" x2="12" y2="50" stroke="#1f2937" stroke-width="1.5" stroke-linecap="round"/>
                                            
                                            <!-- 8 minor hour markers -->
                                            <line x1="73.2" y1="15.4" x2="71.5" y2="17.1" stroke="#4b5563" stroke-width="1" stroke-linecap="round"/>
                                            <line x1="84.6" y1="26.8" x2="82.9" y2="28.5" stroke="#4b5563" stroke-width="1" stroke-linecap="round"/>
                                            <line x1="84.6" y1="73.2" x2="82.9" y2="71.5" stroke="#4b5563" stroke-width="1" stroke-linecap="round"/>
                                            <line x1="73.2" y1="84.6" x2="71.5" y2="82.9" stroke="#4b5563" stroke-width="1" stroke-linecap="round"/>
                                            <line x1="26.8" y1="84.6" x2="28.5" y2="82.9" stroke="#4b5563" stroke-width="1" stroke-linecap="round"/>
                                            <line x1="15.4" y1="73.2" x2="17.1" y2="71.5" stroke="#4b5563" stroke-width="1" stroke-linecap="round"/>
                                            <line x1="15.4" y1="26.8" x2="17.1" y2="28.5" stroke="#4b5563" stroke-width="1" stroke-linecap="round"/>
                                            <line x1="26.8" y1="15.4" x2="28.5" y2="17.1" stroke="#4b5563" stroke-width="1" stroke-linecap="round"/>
                                        </svg>
                                        
                                        <!-- Clock Hands Container -->
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <!-- Hour Hand - Shorter, wider -->
                                            <div id="hour-hand" class="absolute" style="left: 50%; top: 50%; transform-origin: 50% 100%; transform: translate(-50%, -100%) rotate(0deg);">
                                                <div class="w-0.5 h-2.5 bg-gray-900 rounded-full shadow-sm"></div>
                                            </div>
                                            <!-- Minute Hand - Longer, thinner -->
                                            <div id="minute-hand" class="absolute" style="left: 50%; top: 50%; transform-origin: 50% 100%; transform: translate(-50%, -100%) rotate(0deg);">
                                                <div class="w-0.5 h-3.5 bg-gray-900 rounded-full shadow-sm"></div>
                                            </div>
                                            <!-- Second Hand - Thin and red -->
                                            <div id="second-hand" class="absolute" style="left: 50%; top: 50%; transform-origin: 50% 100%; transform: translate(-50%, -100%) rotate(0deg);">
                                                <div class="w-px h-3.5 bg-red-500 rounded-full opacity-80"></div>
                                            </div>
                                            <!-- Center Dot -->
                                            <div class="absolute left-1/2 top-1/2 w-1 h-1 bg-gray-900 rounded-full ring-1 ring-white shadow-md -translate-x-1/2 -translate-y-1/2 z-10"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Crown - Smaller and more realistic -->
                            <div class="absolute -right-0.5 top-1/2 transform -translate-y-1/2 w-1 h-4 bg-gradient-to-br from-gray-600 to-gray-800 rounded-full shadow-md"></div>
                        </div>
                        
                        <!-- Digital Time Display -->
                        <div class="flex flex-col gap-0.5">
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-base lg:text-lg font-bold bg-gradient-to-r from-gray-900 via-purple-800 to-teal-800 bg-clip-text text-transparent" id="current-time">{{ now()->setTimezone($businessTimezone ?? 'Asia/Karachi')->format('g:i A') }}</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <div class="w-1.5 h-1.5 bg-gradient-to-br from-purple-500 to-teal-500 rounded-full animate-pulse"></div>
                                <span class="text-xs text-gray-500 font-medium">Live</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Alert -->
    <x-success-alert message="You're Successfully logged in!" />


    <!-- Key Statistics Cards - Enhanced Design -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Sales Card -->
        <div class="relative bg-gradient-to-br from-emerald-50 via-emerald-50 to-emerald-100 rounded-xl shadow-sm border border-emerald-100 p-6 overflow-hidden group hover:shadow-lg transition-all duration-300">
            <!-- Decorative elements -->
            <div class="absolute top-0 right-0 w-24 h-24 opacity-10">
                <svg viewBox="0 0 100 100" class="w-full h-full">
                    <circle cx="50" cy="50" r="40" fill="none" stroke="currentColor" stroke-width="1"/>
                    <circle cx="50" cy="50" r="25" fill="none" stroke="currentColor" stroke-width="1"/>
                    <circle cx="50" cy="50" r="10" fill="currentColor"/>
                </svg>
            </div>
            <div class="absolute bottom-0 left-0 w-16 h-16 bg-emerald-200 rounded-full -ml-8 -mb-8 opacity-20"></div>

            <div class="relative flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-md group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-emerald-700 uppercase tracking-wide">Total Sales</p>
                        <div class="flex items-center space-x-1">
                            <div class="w-2 h-2 bg-emerald-400 rounded-full"></div>
                            <div class="w-1 h-1 bg-emerald-300 rounded-full"></div>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-emerald-900 mt-1">{{ number_format($stats['total_sales'] ?? 0) }}</p>
                    <div class="flex items-center mt-3">
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-emerald-100 text-emerald-700">
                            {{ $todayStats['today_sales'] ?? 0 }} today • {{ $statusDistributions['sales']['posted'] ?? 0 }} posted
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Purchases Card -->
        <div class="relative bg-gradient-to-br from-blue-50 via-blue-50 to-blue-100 rounded-xl shadow-sm border border-blue-100 p-6 overflow-hidden group hover:shadow-lg transition-all duration-300">
            <!-- Decorative elements -->
            <div class="absolute top-0 right-0 w-24 h-24 opacity-10">
                <svg viewBox="0 0 100 100" class="w-full h-full">
                    <circle cx="50" cy="50" r="40" fill="none" stroke="currentColor" stroke-width="1"/>
                    <circle cx="50" cy="50" r="25" fill="none" stroke="currentColor" stroke-width="1"/>
                    <circle cx="50" cy="50" r="10" fill="currentColor"/>
                </svg>
            </div>
            <div class="absolute bottom-0 left-0 w-16 h-16 bg-blue-200 rounded-full -ml-8 -mb-8 opacity-20"></div>

            <div class="relative flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-md group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-blue-700 uppercase tracking-wide">Total Purchases</p>
                        <div class="flex items-center space-x-1">
                            <div class="w-2 h-2 bg-blue-400 rounded-full"></div>
                            <div class="w-1 h-1 bg-blue-300 rounded-full"></div>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-blue-900 mt-1">{{ number_format($stats['total_purchases'] ?? 0) }}</p>
                    <div class="flex items-center mt-3">
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-700">
                            {{ $todayStats['today_purchases'] ?? 0 }} today • {{ $statusDistributions['purchases']['posted'] ?? 0 }} posted
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Stock Card -->
        <div class="relative bg-gradient-to-br from-purple-50 via-purple-50 to-purple-100 rounded-xl shadow-sm border border-purple-100 p-6 overflow-hidden group hover:shadow-lg transition-all duration-300">
            <!-- Decorative elements -->
            <div class="absolute top-0 right-0 w-24 h-24 opacity-10">
                <svg viewBox="0 0 100 100" class="w-full h-full">
                    <circle cx="50" cy="50" r="40" fill="none" stroke="currentColor" stroke-width="1"/>
                    <circle cx="50" cy="50" r="25" fill="none" stroke="currentColor" stroke-width="1"/>
                    <circle cx="50" cy="50" r="10" fill="currentColor"/>
                </svg>
            </div>
            <div class="absolute bottom-0 left-0 w-16 h-16 bg-purple-200 rounded-full -ml-8 -mb-8 opacity-20"></div>

            <div class="relative flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-md group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-purple-700 uppercase tracking-wide">Current Stock</p>
                        <div class="flex items-center space-x-1">
                            <div class="w-2 h-2 bg-purple-400 rounded-full"></div>
                            <div class="w-1 h-1 bg-purple-300 rounded-full"></div>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-purple-900 mt-1">{{ number_format($inventoryStats['general_items_stock'] ?? 0) }}</p>
                    <div class="flex items-center mt-3">
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-purple-100 text-purple-700">
                            {{ $stats['total_general_items'] ?? 0 }} items
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Revenue Card -->
        <div class="relative bg-gradient-to-br from-teal-50 via-teal-50 to-teal-100 rounded-xl shadow-sm border border-teal-100 p-6 overflow-hidden group hover:shadow-lg transition-all duration-300">
            <!-- Decorative elements -->
            <div class="absolute top-0 right-0 w-24 h-24 opacity-10">
                <svg viewBox="0 0 100 100" class="w-full h-full">
                    <circle cx="50" cy="50" r="40" fill="none" stroke="currentColor" stroke-width="1"/>
                    <circle cx="50" cy="50" r="25" fill="none" stroke="currentColor" stroke-width="1"/>
                    <circle cx="50" cy="50" r="10" fill="currentColor"/>
                </svg>
            </div>
            <div class="absolute bottom-0 left-0 w-16 h-16 bg-teal-200 rounded-full -ml-8 -mb-8 opacity-20"></div>

            <div class="relative flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl flex items-center justify-center shadow-md group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-teal-700 uppercase tracking-wide">Sales Revenue</p>
                        <div class="flex items-center space-x-1">
                            <div class="w-2 h-2 bg-teal-400 rounded-full"></div>
                            <div class="w-1 h-1 bg-teal-300 rounded-full"></div>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-teal-900 mt-1">PKR {{ number_format($financialStats['total_sales_revenue'] ?? 0, 0) }}</p>
                    <div class="flex items-center mt-3">
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-teal-100 text-teal-700">
                            PKR {{ number_format($todayStats['today_sales_amount'] ?? 0, 0) }} today
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Section -->
    <div class="relative backdrop-blur-xl bg-white/70 rounded-2xl shadow-xl shadow-purple-500/5 border border-white/60 p-6 lg:p-8 mb-8 overflow-hidden group">
        <!-- Animated Background Orbs -->
        <div class="absolute -top-20 -left-20 w-40 h-40 bg-gradient-to-br from-purple-400/10 to-teal-400/10 rounded-full blur-3xl group-hover:scale-125 transition-transform duration-700"></div>
        <div class="absolute -bottom-20 -right-20 w-40 h-40 bg-gradient-to-br from-purple-400/10 to-indigo-400/10 rounded-full blur-3xl group-hover:scale-125 transition-transform duration-700"></div>
        
        <!-- Section Header -->
        <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-br from-purple-500 to-teal-600 rounded-xl blur-lg opacity-40"></div>
                        <div class="relative bg-gradient-to-br from-purple-600 to-teal-600 p-2.5 rounded-xl shadow-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h2 class="text-xl lg:text-2xl font-bold bg-gradient-to-r from-gray-900 via-purple-800 to-teal-800 bg-clip-text text-transparent">
                            Quick Actions
                        </h2>
                        <p class="text-sm text-gray-600 mt-0.5">Access your most frequently used features</p>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-2 px-4 py-2 backdrop-blur-sm bg-gradient-to-r from-emerald-50/80 to-teal-50/80 rounded-xl border border-emerald-200/50 shadow-sm">
                <div class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </div>
                <span class="text-xs font-semibold text-gray-700">Live System</span>
            </div>
        </div>
        
        <!-- Quick Actions Grid -->
        <div class="relative grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 xl:grid-cols-6 gap-3 lg:gap-4">
            @can('create sales')
            <a href="{{ route('sale-invoices.create') }}"
                class="group relative backdrop-blur-lg bg-white/90 rounded-xl border border-white/60 p-4 lg:p-5 hover:shadow-xl hover:shadow-emerald-500/20 hover:-translate-y-1 hover:scale-105 transition-all duration-300 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/0 to-green-500/0 group-hover:from-emerald-500/10 group-hover:to-green-500/5 transition-all duration-300 rounded-xl"></div>
                <div class="absolute -top-6 -right-6 w-16 h-16 bg-emerald-400/20 rounded-full blur-xl group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative flex flex-col items-center text-center space-y-3">
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl blur-md opacity-0 group-hover:opacity-60 transition-opacity duration-300"></div>
                        <div class="relative w-12 h-12 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg group-hover:rotate-6 transition-all duration-300">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <span class="text-sm font-bold text-gray-900 group-hover:text-emerald-700 transition-colors duration-200 block">New Sale</span>
                        <span class="text-xs text-gray-500 mt-0.5 block">Create invoice</span>
                    </div>
                </div>
            </a>
            @endcan

            @can('create sales')
            <a href="{{ route('approvals.create') }}"
                class="group relative backdrop-blur-lg bg-white/90 rounded-xl border border-white/60 p-4 lg:p-5 hover:shadow-xl hover:shadow-teal-500/20 hover:-translate-y-1 hover:scale-105 transition-all duration-300 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-teal-500/0 to-cyan-500/0 group-hover:from-teal-500/10 group-hover:to-cyan-500/5 transition-all duration-300 rounded-xl"></div>
                <div class="absolute -top-6 -right-6 w-16 h-16 bg-teal-400/20 rounded-full blur-xl group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative flex flex-col items-center text-center space-y-3">
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-br from-teal-500 to-cyan-600 rounded-xl blur-md opacity-0 group-hover:opacity-60 transition-opacity duration-300"></div>
                        <div class="relative w-12 h-12 bg-gradient-to-br from-teal-500 to-cyan-600 rounded-xl flex items-center justify-center shadow-lg group-hover:rotate-6 transition-all duration-300">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <span class="text-sm font-bold text-gray-900 group-hover:text-teal-700 transition-colors duration-200 block">New Approval</span>
                        <span class="text-xs text-gray-500 mt-0.5 block">Give on approval</span>
                    </div>
                </div>
            </a>
            @endcan
            
            <a href="{{ route('approvals.index') }}"
                class="group relative backdrop-blur-lg bg-white/90 rounded-xl border border-white/60 p-4 lg:p-5 hover:shadow-xl hover:shadow-blue-500/20 hover:-translate-y-1 hover:scale-105 transition-all duration-300 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/0 to-indigo-500/0 group-hover:from-blue-500/10 group-hover:to-indigo-500/5 transition-all duration-300 rounded-xl"></div>
                <div class="absolute -top-6 -right-6 w-16 h-16 bg-blue-400/20 rounded-full blur-xl group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative flex flex-col items-center text-center space-y-3">
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl blur-md opacity-0 group-hover:opacity-60 transition-opacity duration-300"></div>
                        <div class="relative w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg group-hover:rotate-6 transition-all duration-300">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <span class="text-sm font-bold text-gray-900 group-hover:text-blue-700 transition-colors duration-200 block">Approvals</span>
                        <span class="text-xs text-gray-500 mt-0.5 block">View pending</span>
                    </div>
                </div>
            </a>

            @can('create purchases')
            <a href="{{ route('purchases.create') }}"
                class="group relative backdrop-blur-lg bg-white/90 rounded-xl border border-white/60 p-4 lg:p-5 hover:shadow-xl hover:shadow-purple-500/20 hover:-translate-y-1 hover:scale-105 transition-all duration-300 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-purple-500/0 to-indigo-500/0 group-hover:from-purple-500/10 group-hover:to-indigo-500/5 transition-all duration-300 rounded-xl"></div>
                <div class="absolute -top-6 -right-6 w-16 h-16 bg-purple-400/20 rounded-full blur-xl group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative flex flex-col items-center text-center space-y-3">
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl blur-md opacity-0 group-hover:opacity-60 transition-opacity duration-300"></div>
                        <div class="relative w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg group-hover:rotate-6 transition-all duration-300">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <span class="text-sm font-bold text-gray-900 group-hover:text-purple-700 transition-colors duration-200 block">New Purchase</span>
                        <span class="text-xs text-gray-500 mt-0.5 block">Place order</span>
                    </div>
                </div>
            </a>
            @endcan

            @can('view parties')
            <a href="{{ route('parties.index') }}"
                class="group relative backdrop-blur-lg bg-white/90 rounded-xl border border-white/60 p-4 lg:p-5 hover:shadow-xl hover:shadow-purple-500/20 hover:-translate-y-1 hover:scale-105 transition-all duration-300 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-purple-500/0 to-pink-500/0 group-hover:from-purple-500/10 group-hover:to-pink-500/5 transition-all duration-300 rounded-xl"></div>
                <div class="absolute -top-6 -right-6 w-16 h-16 bg-purple-400/20 rounded-full blur-xl group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative flex flex-col items-center text-center space-y-3">
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl blur-md opacity-0 group-hover:opacity-60 transition-opacity duration-300"></div>
                        <div class="relative w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg group-hover:rotate-6 transition-all duration-300">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <span class="text-sm font-bold text-gray-900 group-hover:text-purple-700 transition-colors duration-200 block">Parties</span>
                        <span class="text-xs text-gray-500 mt-0.5 block">All contacts</span>
                    </div>
                </div>
            </a>
            @endcan
        </div>
    </div>

    <!-- Status Distribution Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Sales Status Chart -->
        <div class="relative backdrop-blur-xl bg-white/80 rounded-2xl shadow-xl shadow-emerald-500/5 border border-white/60 p-6 lg:p-8 overflow-hidden group">
            <div class="absolute -top-16 -right-16 w-40 h-40 bg-gradient-to-br from-emerald-400/15 to-green-400/10 rounded-full blur-3xl group-hover:scale-125 transition-transform duration-700"></div>
            
            <!-- Section Header -->
            <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <div class="absolute inset-0 bg-emerald-500 rounded-xl blur-lg opacity-40"></div>
                        <div class="relative bg-gradient-to-br from-emerald-500 to-green-600 p-2.5 rounded-xl shadow-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg lg:text-xl font-bold text-gray-900">Sales Status</h3>
                        <p class="text-sm text-gray-500 mt-0.5">Current status distribution</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-2xl lg:text-3xl font-bold bg-gradient-to-r from-gray-900 to-emerald-900 bg-clip-text text-transparent">{{ $stats['total_sales'] ?? 0 }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">Total</div>
                </div>
            </div>
            
            <div class="relative space-y-3">
                <!-- Modern Segmented Status Pills -->
                <div class="backdrop-blur-sm bg-gradient-to-r from-white/90 to-white/70 rounded-lg p-4 border border-white/60 shadow-md hover:shadow-lg transition-all duration-300">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="relative">
                                <div class="absolute inset-0 bg-emerald-500 rounded-lg blur opacity-50"></div>
                                <div class="relative w-9 h-9 bg-gradient-to-br from-emerald-500 to-green-600 rounded-lg flex items-center justify-center shadow-md">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-gray-900">Posted</div>
                                <div class="text-xs text-gray-500">Finalized sales</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xl font-bold text-emerald-600">{{ $statusDistributions['sales']['posted'] }}</div>
                            <div class="text-xs text-gray-500">invoices</div>
                        </div>
                    </div>
                    <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-emerald-500 to-green-600 rounded-full transition-all duration-700 shadow-sm" style="width: {{ $statusDistributions['sales']['posted'] > 0 ? ($statusDistributions['sales']['posted'] / ($statusDistributions['sales']['posted'] + $statusDistributions['sales']['draft'] + $statusDistributions['sales']['cancelled'])) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <div class="backdrop-blur-sm bg-gradient-to-r from-white/90 to-white/70 rounded-lg p-4 border border-white/60 shadow-md hover:shadow-lg transition-all duration-300">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="relative">
                                <div class="absolute inset-0 bg-amber-500 rounded-lg blur opacity-50"></div>
                                <div class="relative w-9 h-9 bg-gradient-to-br from-amber-400 to-orange-500 rounded-lg flex items-center justify-center shadow-md">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-gray-900">Draft</div>
                                <div class="text-xs text-gray-500">Pending completion</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xl font-bold text-amber-600">{{ $statusDistributions['sales']['draft'] }}</div>
                            <div class="text-xs text-gray-500">invoices</div>
                        </div>
                    </div>
                    <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-amber-400 to-orange-500 rounded-full transition-all duration-700 shadow-sm" style="width: {{ $statusDistributions['sales']['draft'] > 0 ? ($statusDistributions['sales']['draft'] / ($statusDistributions['sales']['posted'] + $statusDistributions['sales']['draft'] + $statusDistributions['sales']['cancelled'])) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <div class="backdrop-blur-sm bg-gradient-to-r from-white/90 to-white/70 rounded-lg p-4 border border-white/60 shadow-md hover:shadow-lg transition-all duration-300">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="relative">
                                <div class="absolute inset-0 bg-red-500 rounded-lg blur opacity-50"></div>
                                <div class="relative w-9 h-9 bg-gradient-to-br from-red-500 to-rose-600 rounded-lg flex items-center justify-center shadow-md">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-gray-900">Cancelled</div>
                                <div class="text-xs text-gray-500">Voided transactions</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xl font-bold text-red-600">{{ $statusDistributions['sales']['cancelled'] }}</div>
                            <div class="text-xs text-gray-500">invoices</div>
                        </div>
                    </div>
                    <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-red-500 to-rose-600 rounded-full transition-all duration-700 shadow-sm" style="width: {{ $statusDistributions['sales']['cancelled'] > 0 ? ($statusDistributions['sales']['cancelled'] / ($statusDistributions['sales']['posted'] + $statusDistributions['sales']['draft'] + $statusDistributions['sales']['cancelled'])) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Purchase Status Chart -->
        <div class="relative backdrop-blur-xl bg-white/80 rounded-2xl shadow-xl shadow-blue-500/5 border border-white/60 p-6 lg:p-8 overflow-hidden group">
            <div class="absolute -top-16 -right-16 w-40 h-40 bg-gradient-to-br from-blue-400/15 to-indigo-400/10 rounded-full blur-3xl group-hover:scale-125 transition-transform duration-700"></div>
            
            <!-- Section Header -->
            <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <div class="absolute inset-0 bg-blue-500 rounded-xl blur-lg opacity-40"></div>
                        <div class="relative bg-gradient-to-br from-blue-500 to-indigo-600 p-2.5 rounded-xl shadow-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg lg:text-xl font-bold text-gray-900">Purchase Status</h3>
                        <p class="text-sm text-gray-500 mt-0.5">Current status distribution</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-2xl lg:text-3xl font-bold bg-gradient-to-r from-gray-900 to-blue-900 bg-clip-text text-transparent">{{ $stats['total_purchases'] ?? 0 }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">Total</div>
                </div>
            </div>
            
            <div class="relative space-y-3">
                <div class="backdrop-blur-sm bg-gradient-to-r from-white/90 to-white/70 rounded-lg p-4 border border-white/60 shadow-md hover:shadow-lg transition-all duration-300">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="relative">
                                <div class="absolute inset-0 bg-blue-500 rounded-lg blur opacity-50"></div>
                                <div class="relative w-9 h-9 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-md">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-gray-900">Posted</div>
                                <div class="text-xs text-gray-500">Finalized purchases</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xl font-bold text-blue-600">{{ $statusDistributions['purchases']['posted'] }}</div>
                            <div class="text-xs text-gray-500">orders</div>
                        </div>
                    </div>
                    <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full transition-all duration-700 shadow-sm" style="width: {{ $statusDistributions['purchases']['posted'] > 0 ? ($statusDistributions['purchases']['posted'] / ($statusDistributions['purchases']['posted'] + $statusDistributions['purchases']['draft'] + $statusDistributions['purchases']['cancelled'])) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <div class="backdrop-blur-sm bg-gradient-to-r from-white/90 to-white/70 rounded-lg p-4 border border-white/60 shadow-md hover:shadow-lg transition-all duration-300">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="relative">
                                <div class="absolute inset-0 bg-amber-500 rounded-lg blur opacity-50"></div>
                                <div class="relative w-9 h-9 bg-gradient-to-br from-amber-400 to-orange-500 rounded-lg flex items-center justify-center shadow-md">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-gray-900">Draft</div>
                                <div class="text-xs text-gray-500">Pending completion</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xl font-bold text-amber-600">{{ $statusDistributions['purchases']['draft'] }}</div>
                            <div class="text-xs text-gray-500">orders</div>
                        </div>
                    </div>
                    <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-amber-400 to-orange-500 rounded-full transition-all duration-700 shadow-sm" style="width: {{ $statusDistributions['purchases']['draft'] > 0 ? ($statusDistributions['purchases']['draft'] / ($statusDistributions['purchases']['posted'] + $statusDistributions['purchases']['draft'] + $statusDistributions['purchases']['cancelled'])) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <div class="backdrop-blur-sm bg-gradient-to-r from-white/90 to-white/70 rounded-lg p-4 border border-white/60 shadow-md hover:shadow-lg transition-all duration-300">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="relative">
                                <div class="absolute inset-0 bg-red-500 rounded-lg blur opacity-50"></div>
                                <div class="relative w-9 h-9 bg-gradient-to-br from-red-500 to-rose-600 rounded-lg flex items-center justify-center shadow-md">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-gray-900">Cancelled</div>
                                <div class="text-xs text-gray-500">Voided transactions</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xl font-bold text-red-600">{{ $statusDistributions['purchases']['cancelled'] }}</div>
                            <div class="text-xs text-gray-500">orders</div>
                        </div>
                    </div>
                    <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-red-500 to-rose-600 rounded-full transition-all duration-700 shadow-sm" style="width: {{ $statusDistributions['purchases']['cancelled'] > 0 ? ($statusDistributions['purchases']['cancelled'] / ($statusDistributions['purchases']['posted'] + $statusDistributions['purchases']['draft'] + $statusDistributions['purchases']['cancelled'])) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Recent Sales Timeline -->
        <div class="relative backdrop-blur-xl bg-white/80 rounded-2xl shadow-xl shadow-emerald-500/5 border border-white/60 p-6 lg:p-8 overflow-hidden group">
            <div class="absolute -top-16 -left-16 w-40 h-40 bg-gradient-to-br from-emerald-400/10 to-green-400/5 rounded-full blur-3xl group-hover:scale-125 transition-transform duration-700"></div>
            
            <!-- Section Header -->
            <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <div class="absolute inset-0 bg-emerald-500 rounded-xl blur-lg opacity-40"></div>
                        <div class="relative bg-gradient-to-br from-emerald-500 to-green-600 p-2.5 rounded-xl shadow-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg lg:text-xl font-bold text-gray-900">Recent Sales</h3>
                        <p class="text-sm text-gray-500 mt-0.5">Latest sales activities</p>
                    </div>
                </div>
                <a href="{{ route('sale-invoices.index') }}" class="group flex items-center space-x-2 px-4 py-2 backdrop-blur-sm bg-gradient-to-r from-emerald-50/80 to-green-50/80 hover:from-emerald-100/80 hover:to-green-100/80 rounded-xl border border-emerald-200/50 transition-all duration-300 hover:shadow-md">
                    <span class="text-xs font-semibold text-emerald-700">View all</span>
                    <svg class="w-4 h-4 text-emerald-600 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
            
            <div class="relative space-y-3">
                @forelse($recentActivities['recent_sales'] as $index => $sale)
                <div class="relative group/item">
                    <!-- Timeline connector -->
                    @if(!$loop->last)
                    <div class="absolute left-5 top-12 bottom-0 w-0.5 bg-gradient-to-b from-emerald-200 to-transparent"></div>
                    @endif
                    
                    <div class="relative flex items-start space-x-3 p-3.5 backdrop-blur-sm bg-white/90 rounded-lg border border-white/60 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">
                        <!-- Timeline dot with glow -->
                        <div class="relative flex-shrink-0">
                            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500 to-green-600 rounded-lg blur-md opacity-0 group-hover/item:opacity-60 transition-opacity duration-300"></div>
                            <div class="relative w-10 h-10 bg-gradient-to-br from-emerald-500 to-green-600 rounded-lg flex items-center justify-center shadow-md group-hover/item:scale-110 group-hover/item:rotate-6 transition-all duration-300">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-sm font-bold text-gray-900 group-hover/item:text-emerald-700 transition-colors duration-200">Sale #{{ $sale->invoice_number }}</span>
                                <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">{{ $sale->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-gray-700 font-medium mb-2">{{ $sale->party->name ?? 'Cash Sale' }}</p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-1.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold shadow-sm {{ $sale->status === 'posted' ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : ($sale->status === 'draft' ? 'bg-amber-100 text-amber-700 border border-amber-200' : 'bg-red-100 text-red-700 border border-red-200') }}">
                                        {{ ucfirst($sale->status) }}
                                    </span>
                                    <span class="text-xs font-medium text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded">{{ ucfirst($sale->sale_type) }}</span>
                                </div>
                                <span class="text-sm font-bold text-gray-900">PKR {{ number_format($sale->total_amount) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-12">
                    <div class="relative mx-auto mb-4 w-16 h-16">
                        <div class="absolute inset-0 bg-gray-200 rounded-full blur-xl opacity-50"></div>
                        <div class="relative w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center shadow-lg">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900 mb-1">No sales yet</h3>
                    <p class="text-xs text-gray-500">Get started by creating your first sale.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Purchases Timeline -->
        <div class="relative backdrop-blur-xl bg-white/80 rounded-2xl shadow-xl shadow-blue-500/5 border border-white/60 p-6 lg:p-8 overflow-hidden group">
            <div class="absolute -top-16 -left-16 w-40 h-40 bg-gradient-to-br from-blue-400/10 to-indigo-400/5 rounded-full blur-3xl group-hover:scale-125 transition-transform duration-700"></div>
            
            <!-- Section Header -->
            <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <div class="absolute inset-0 bg-blue-500 rounded-xl blur-lg opacity-40"></div>
                        <div class="relative bg-gradient-to-br from-blue-500 to-indigo-600 p-2.5 rounded-xl shadow-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg lg:text-xl font-bold text-gray-900">Recent Purchases</h3>
                        <p class="text-sm text-gray-500 mt-0.5">Latest purchase activities</p>
                    </div>
                </div>
                <a href="{{ route('purchases.index') }}" class="group flex items-center space-x-2 px-4 py-2 backdrop-blur-sm bg-gradient-to-r from-blue-50/80 to-indigo-50/80 hover:from-blue-100/80 hover:to-indigo-100/80 rounded-xl border border-blue-200/50 transition-all duration-300 hover:shadow-md">
                    <span class="text-xs font-semibold text-blue-700">View all</span>
                    <svg class="w-4 h-4 text-blue-600 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
            
            <div class="relative space-y-3">
                @forelse($recentActivities['recent_purchases'] as $purchase)
                <div class="relative group/item">
                    @if(!$loop->last)
                    <div class="absolute left-5 top-12 bottom-0 w-0.5 bg-gradient-to-b from-blue-200 to-transparent"></div>
                    @endif
                    
                    <div class="relative flex items-start space-x-3 p-3.5 backdrop-blur-sm bg-white/90 rounded-lg border border-white/60 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">
                        <div class="relative flex-shrink-0">
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg blur-md opacity-0 group-hover/item:opacity-60 transition-opacity duration-300"></div>
                            <div class="relative w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-md group-hover/item:scale-110 group-hover/item:rotate-6 transition-all duration-300">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                            </div>
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-sm font-bold text-gray-900 group-hover/item:text-blue-700 transition-colors duration-200">Purchase #{{ $purchase->invoice_number }}</span>
                                <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">{{ $purchase->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-gray-700 font-medium mb-2">{{ $purchase->party->name ?? 'Cash Purchase' }}</p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-1.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold shadow-sm {{ $purchase->status === 'posted' ? 'bg-blue-100 text-blue-700 border border-blue-200' : ($purchase->status === 'draft' ? 'bg-amber-100 text-amber-700 border border-amber-200' : 'bg-red-100 text-red-700 border border-red-200') }}">
                                        {{ ucfirst($purchase->status) }}
                                    </span>
                                    <span class="text-xs font-medium text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded">{{ ucfirst($purchase->payment_type) }}</span>
                                </div>
                                <span class="text-sm font-bold text-gray-900">PKR {{ number_format($purchase->total_amount) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-12">
                    <div class="relative mx-auto mb-4 w-16 h-16">
                        <div class="absolute inset-0 bg-gray-200 rounded-full blur-xl opacity-50"></div>
                        <div class="relative w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center shadow-lg">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900 mb-1">No purchases yet</h3>
                    <p class="text-xs text-gray-500">Get started by creating your first purchase.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>






    <!-- Inventory Alerts & Reports Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Low Stock Alerts -->
        <div class="relative backdrop-blur-xl bg-white/80 rounded-2xl shadow-xl shadow-amber-500/5 border border-white/60 p-6 lg:p-8 overflow-hidden group">
            <div class="absolute -top-16 -right-16 w-40 h-40 bg-gradient-to-br from-amber-400/10 to-orange-400/5 rounded-full blur-3xl group-hover:scale-125 transition-transform duration-700"></div>
            
            <!-- Section Header -->
            <div class="relative mb-6">
                <div class="flex items-center gap-3 mb-2">
                    <div class="relative">
                        <div class="absolute inset-0 bg-amber-500 rounded-xl blur-lg opacity-40"></div>
                        <div class="relative bg-gradient-to-br from-amber-500 to-orange-600 p-2.5 rounded-xl shadow-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Low Stock</h3>
                        <p class="text-sm text-gray-500 mt-0.5">Items needing restocking</p>
                    </div>
                </div>
            </div>
            
            <div class="relative space-y-3">
                @if($inventoryStats['low_stock_items'] > 0)
                    <div class="backdrop-blur-sm bg-gradient-to-r from-amber-50/90 to-orange-50/80 rounded-lg p-4 border border-amber-200/50 shadow-md hover:shadow-lg transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="relative">
                                    <div class="absolute inset-0 bg-amber-500 rounded-lg blur opacity-50"></div>
                                    <div class="relative w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-600 rounded-lg flex items-center justify-center shadow-md">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <div class="text-xl font-bold text-amber-700">{{ $inventoryStats['low_stock_items'] }}</div>
                                    <div class="text-xs text-gray-600 font-medium">Items below reorder level</div>
                                </div>
                            </div>
                            <a href="{{ route('general-items.index') }}" class="px-3 py-1.5 bg-white/80 hover:bg-white rounded-lg border border-amber-200 text-amber-700 hover:text-amber-800 font-semibold text-xs transition-all duration-300 hover:shadow-md">
                                View
                            </a>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="relative mx-auto mb-3 w-14 h-14">
                            <div class="absolute inset-0 bg-emerald-200 rounded-full blur-xl opacity-30"></div>
                            <div class="relative w-14 h-14 bg-gradient-to-br from-emerald-100 to-green-200 rounded-full flex items-center justify-center shadow-lg">
                                <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-sm font-bold text-gray-900 mb-1">All items well stocked</h3>
                        <p class="text-xs text-gray-500">No low stock alerts</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Reports -->
        <div class="relative backdrop-blur-xl bg-white/80 rounded-2xl shadow-xl shadow-purple-500/5 border border-white/60 p-6 lg:p-8 overflow-hidden group">
            <div class="absolute -top-16 -right-16 w-40 h-40 bg-gradient-to-br from-purple-400/10 to-indigo-400/5 rounded-full blur-3xl group-hover:scale-125 transition-transform duration-700"></div>
            
            <!-- Section Header -->
            <div class="relative mb-6">
                <div class="flex items-center gap-3 mb-2">
                    <div class="relative">
                        <div class="absolute inset-0 bg-purple-500 rounded-xl blur-lg opacity-40"></div>
                        <div class="relative bg-gradient-to-br from-purple-600 to-indigo-600 p-2.5 rounded-xl shadow-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Quick Reports</h3>
                        <p class="text-sm text-gray-500 mt-0.5">Access important reports</p>
                    </div>
                </div>
            </div>
            
            <div class="relative space-y-2.5">
                <a href="{{ route('banks.balances-report') }}" class="group/link flex items-center justify-between p-3 backdrop-blur-sm bg-white/90 rounded-lg border border-white/60 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">
                    <div class="flex items-center space-x-3">
                        <div class="relative">
                            <div class="absolute inset-0 bg-blue-500 rounded-lg blur opacity-0 group-hover/link:opacity-50 transition-opacity duration-300"></div>
                            <div class="relative w-9 h-9 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-md group-hover/link:scale-110 transition-transform duration-300">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <span class="text-sm font-semibold text-gray-900 group-hover/link:text-blue-700 transition-colors">Bank Balances</span>
                            <div class="text-xs text-gray-500">Current accounts</div>
                        </div>
                    </div>
                    <svg class="h-4 w-4 text-gray-400 group-hover/link:text-blue-600 group-hover/link:translate-x-1 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>

                <a href="{{ route('parties.balances-report') }}" class="group/link flex items-center justify-between p-3 backdrop-blur-sm bg-white/90 rounded-lg border border-white/60 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">
                    <div class="flex items-center space-x-3">
                        <div class="relative">
                            <div class="absolute inset-0 bg-purple-500 rounded-lg blur opacity-0 group-hover/link:opacity-50 transition-opacity duration-300"></div>
                            <div class="relative w-9 h-9 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center shadow-md group-hover/link:scale-110 transition-transform duration-300">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <span class="text-sm font-semibold text-gray-900 group-hover/link:text-purple-700 transition-colors">Party Balances</span>
                            <div class="text-xs text-gray-500">Customer & supplier</div>
                        </div>
                    </div>
                    <svg class="h-4 w-4 text-gray-400 group-hover/link:text-purple-600 group-hover/link:translate-x-1 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>

                {{-- Arms History link disabled - StoreBook is items-only --}}
                {{-- <a href="{{ route('arms-history') }}" class="group/link flex items-center justify-between p-3 backdrop-blur-sm bg-white/90 rounded-lg border border-white/60 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">
                    <div class="flex items-center space-x-3">
                        <div class="relative">
                            <div class="absolute inset-0 bg-rose-500 rounded-lg blur opacity-0 group-hover/link:opacity-50 transition-opacity duration-300"></div>
                            <div class="relative w-9 h-9 bg-gradient-to-br from-rose-500 to-red-600 rounded-lg flex items-center justify-center shadow-md group-hover/link:scale-110 transition-transform duration-300">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <span class="text-sm font-semibold text-gray-900 group-hover/link:text-rose-700 transition-colors">Arms History</span>
                            <div class="text-xs text-gray-500">Firearm transactions</div>
                        </div>
                    </div>
                    <svg class="h-4 w-4 text-gray-400 group-hover/link:text-rose-600 group-hover/link:translate-x-1 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a> --}}
            </div>
        </div>

        <!-- System Overview -->
        <div class="relative backdrop-blur-xl bg-white/80 rounded-2xl shadow-xl shadow-slate-500/5 border border-white/60 p-6 lg:p-8 overflow-hidden group">
            <div class="absolute -top-16 -right-16 w-40 h-40 bg-gradient-to-br from-slate-400/10 to-gray-400/5 rounded-full blur-3xl group-hover:scale-125 transition-transform duration-700"></div>
            
            <!-- Section Header -->
            <div class="relative mb-6">
                <div class="flex items-center gap-3 mb-2">
                    <div class="relative">
                        <div class="absolute inset-0 bg-slate-500 rounded-xl blur-lg opacity-40"></div>
                        <div class="relative bg-gradient-to-br from-slate-600 to-gray-600 p-2.5 rounded-xl shadow-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">System Overview</h3>
                        <p class="text-sm text-gray-500 mt-0.5">Key system metrics</p>
                    </div>
                </div>
            </div>
            
            <div class="relative space-y-3">
                <div class="flex items-center justify-between p-3 backdrop-blur-sm bg-white/90 rounded-lg border border-white/60 hover:shadow-md transition-all duration-300">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-1.5 h-1.5 rounded-full bg-gradient-to-r from-blue-500 to-indigo-500"></div>
                        <span class="text-sm font-semibold text-gray-700">Total Returns</span>
                    </div>
                    <span class="text-base font-bold bg-gradient-to-r from-gray-900 to-blue-900 bg-clip-text text-transparent">{{ $stats['total_sale_returns'] + $stats['total_purchase_returns'] }}</span>
                </div>
                <div class="flex items-center justify-between p-3 backdrop-blur-sm bg-white/90 rounded-lg border border-white/60 hover:shadow-md transition-all duration-300">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-1.5 h-1.5 rounded-full bg-gradient-to-r from-purple-500 to-pink-500"></div>
                        <span class="text-sm font-semibold text-gray-700">Journal Entries</span>
                    </div>
                    <span class="text-base font-bold bg-gradient-to-r from-gray-900 to-purple-900 bg-clip-text text-transparent">{{ number_format($stats['total_journal_entries']) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 backdrop-blur-sm bg-white/90 rounded-lg border border-white/60 hover:shadow-md transition-all duration-300">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-1.5 h-1.5 rounded-full bg-gradient-to-r from-emerald-500 to-teal-500"></div>
                        <span class="text-sm font-semibold text-gray-700">Bank Accounts</span>
                    </div>
                    <span class="text-base font-bold bg-gradient-to-r from-gray-900 to-emerald-900 bg-clip-text text-transparent">{{ $stats['total_banks'] }}</span>
                </div>
                <div class="flex items-center justify-between p-3 backdrop-blur-sm bg-white/90 rounded-lg border border-white/60 hover:shadow-md transition-all duration-300">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-1.5 h-1.5 rounded-full bg-gradient-to-r from-orange-500 to-red-500"></div>
                        <span class="text-sm font-semibold text-gray-700">Total Items</span>
                    </div>
                    <span class="text-base font-bold bg-gradient-to-r from-gray-900 to-orange-900 bg-clip-text text-transparent">{{ $stats['total_general_items']}}</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Business timezone from server
        const businessTimezone = '{{ $businessTimezone ?? "Asia/Karachi" }}';
        
        // Function to update time according to business timezone
        function updateBusinessTime() {
            const now = new Date();
            
            // Convert to business timezone
            const businessTime = new Date(now.toLocaleString("en-US", {timeZone: businessTimezone}));
            
            // Get time components
            const hours = businessTime.getHours();
            const minutes = businessTime.getMinutes();
            const seconds = businessTime.getSeconds();
            
            // Calculate hand rotations
            // Hour hand: 30 degrees per hour + 0.5 degrees per minute
            const hourRotation = (hours % 12) * 30 + minutes * 0.5;
            // Minute hand: 6 degrees per minute + 0.1 degrees per second
            const minuteRotation = minutes * 6 + seconds * 0.1;
            // Second hand: 6 degrees per second
            const secondRotation = seconds * 6;
            
            // Update watch hands
            const hourHand = document.getElementById('hour-hand');
            const minuteHand = document.getElementById('minute-hand');
            const secondHand = document.getElementById('second-hand');
            
            if (hourHand) {
                hourHand.style.transform = `translate(-50%, -100%) rotate(${hourRotation}deg)`;
            }
            if (minuteHand) {
                minuteHand.style.transform = `translate(-50%, -100%) rotate(${minuteRotation}deg)`;
            }
            if (secondHand) {
                secondHand.style.transform = `translate(-50%, -100%) rotate(${secondRotation}deg)`;
            }
            
            // Update day
            const dayElement = document.getElementById('current-day');
            if (dayElement) {
                dayElement.textContent = businessTime.toLocaleDateString('en-US', { weekday: 'long' });
            }
            
            // Update date
            const dateElement = document.getElementById('current-date');
            if (dateElement) {
                dateElement.textContent = businessTime.toLocaleDateString('en-US', { 
                    month: 'short', 
                    day: 'numeric', 
                    year: 'numeric' 
                });
            }
            
            // Update time
            const timeElement = document.getElementById('current-time');
            if (timeElement) {
                timeElement.textContent = businessTime.toLocaleTimeString('en-US', { 
                    hour: 'numeric', 
                    minute: '2-digit',
                    hour12: true 
                });
            }
        }
        
        // Update time immediately
        updateBusinessTime();
        
        // Update time every second for smooth animation
        setInterval(updateBusinessTime, 1000);
    </script>

</x-app-layout>
