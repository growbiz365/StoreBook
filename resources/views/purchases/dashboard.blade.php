<x-app-layout>
    @section('title', 'Purchases Dashboard - StoreBook')
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        ['url' => '/purchases-dashboard', 'label' => 'Purchases Dashboard'],
    ]" />

    <!-- Header Section -->
    <div class="relative bg-gradient-to-r from-green-50 via-white to-white rounded-xl shadow-sm border border-green-100 p-6 mb-6 mt-4 overflow-hidden">
        <!-- Decorative elements -->
        <div class="absolute top-0 right-0 w-32 h-32 opacity-10">
            <svg viewBox="0 0 100 100" class="w-full h-full">
                <circle cx="50" cy="50" r="40" fill="none" stroke="currentColor" stroke-width="1"/>
                <circle cx="50" cy="50" r="25" fill="none" stroke="currentColor" stroke-width="1"/>
                <circle cx="50" cy="50" r="10" fill="currentColor"/>
            </svg>
        </div>

        <div class="relative flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <!-- Enhanced icon -->
                <div class="flex-shrink-0 bg-gradient-to-br from-green-500 to-green-600 p-3 rounded-xl shadow-md">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 bg-gradient-to-r from-gray-900 to-green-700 bg-clip-text text-transparent">
                        Purchases Dashboard
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">Comprehensive overview of your purchase management system</p>
                    <div class="flex items-center space-x-2 mt-1">
                        <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                        <span class="text-xs text-gray-500">Purchase system active</span>
                    </div>
                </div>
            </div>

            <!-- Right section -->
            <div class="flex items-center space-x-4">
                <div class="text-right">
                    <div class="text-sm font-medium text-gray-900" id="current-day">{{ now()->setTimezone($businessTimezone ?? 'Asia/Karachi')->format('l') }}</div>
                    <div class="text-xs text-gray-500" id="current-date">{{ now()->setTimezone($businessTimezone ?? 'Asia/Karachi')->format('M d, Y') }}</div>
                </div>
                <div class="h-8 w-px bg-gray-200"></div>
                <div class="bg-white/70 backdrop-blur-sm rounded-lg px-3 py-2 border border-white/50 shadow-sm">
                    <div class="flex items-center space-x-2">
                        <svg class="h-4 w-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-xs font-medium text-gray-700" id="current-time">{{ now()->setTimezone($businessTimezone ?? 'Asia/Karachi')->format('g:i A') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @can('create purchases')
        <a href="{{ route('purchases.create') }}"
            class="group bg-gradient-to-r from-white to-green-50/30 rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-lg hover:border-green-200 transition-all duration-300 flex items-center space-x-3 hover:-translate-y-0.5">
            <div class="flex-shrink-0 bg-gradient-to-br from-green-500 to-green-600 p-2.5 rounded-xl shadow-md group-hover:shadow-lg group-hover:scale-105 transition-all duration-300">
                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-semibold text-gray-900 group-hover:text-green-700 transition-colors duration-200">New Purchase</h3>
                <p class="text-xs text-gray-500 group-hover:text-gray-600 transition-colors duration-200">Create purchase order</p>
            </div>
            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                <svg class="h-4 w-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        </a>
        @endcan
        @can('view purchases')
        <a href="{{ route('purchases.index') }}"
            class="group bg-gradient-to-r from-white to-blue-50/30 rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-lg hover:border-blue-200 transition-all duration-300 flex items-center space-x-3 hover:-translate-y-0.5">
            <div class="flex-shrink-0 bg-gradient-to-br from-blue-500 to-blue-600 p-2.5 rounded-xl shadow-md group-hover:shadow-lg group-hover:scale-105 transition-all duration-300">
                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-semibold text-gray-900 group-hover:text-blue-700 transition-colors duration-200">All Purchases</h3>
                <p class="text-xs text-gray-500 group-hover:text-gray-600 transition-colors duration-200">View all purchases</p>
            </div>
            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                <svg class="h-4 w-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        </a>
        @endcan
        @can('create purchase returns')
        <a href="{{ route('purchase-returns.create') }}"
            class="group bg-gradient-to-r from-white to-orange-50/30 rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-lg hover:border-orange-200 transition-all duration-300 flex items-center space-x-3 hover:-translate-y-0.5">
            <div class="flex-shrink-0 bg-gradient-to-br from-orange-500 to-orange-600 p-2.5 rounded-xl shadow-md group-hover:shadow-lg group-hover:scale-105 transition-all duration-300">
                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-semibold text-gray-900 group-hover:text-orange-700 transition-colors duration-200">Purchase Return</h3>
                <p class="text-xs text-gray-500 group-hover:text-gray-600 transition-colors duration-200">Create purchase return</p>
            </div>
            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                <svg class="h-4 w-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        </a>
        @endcan
        @can('view purchase returns')
        <a href="{{ route('purchase-returns.index') }}"
            class="group bg-gradient-to-r from-white to-red-50/30 rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-lg hover:border-red-200 transition-all duration-300 flex items-center space-x-3 hover:-translate-y-0.5">
            <div class="flex-shrink-0 bg-gradient-to-br from-red-500 to-red-600 p-2.5 rounded-xl shadow-md group-hover:shadow-lg group-hover:scale-105 transition-all duration-300">
                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-semibold text-gray-900 group-hover:text-red-700 transition-colors duration-200">All Returns</h3>
                <p class="text-xs text-gray-500 group-hover:text-gray-600 transition-colors duration-200">View all purchase returns</p>
            </div>
            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                <svg class="h-4 w-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        </a>
        @endcan
        
    </div>

    <!-- Enhanced Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Total Purchases Statistics Card -->
        <div class="relative bg-gradient-to-br from-green-50 via-green-50 to-green-100 rounded-xl shadow-sm border border-green-100 p-6 overflow-hidden group hover:shadow-lg transition-all duration-300">
            <!-- Decorative elements -->
            <div class="absolute top-0 right-0 w-24 h-24 opacity-10">
                <svg viewBox="0 0 100 100" class="w-full h-full">
                    <circle cx="50" cy="50" r="40" fill="none" stroke="currentColor" stroke-width="1"/>
                    <circle cx="50" cy="50" r="25" fill="none" stroke="currentColor" stroke-width="1"/>
                    <circle cx="50" cy="50" r="10" fill="currentColor"/>
                </svg>
            </div>
            <div class="absolute bottom-0 left-0 w-16 h-16 bg-green-200 rounded-full -ml-8 -mb-8 opacity-20"></div>

            <div class="relative flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-md group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-green-700 uppercase tracking-wide">Total Purchases</p>
                        <div class="flex items-center space-x-1">
                            <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                            <div class="w-1 h-1 bg-green-300 rounded-full"></div>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-green-900 mt-1">{{ number_format($purchaseStats['total_purchases']) }}</p>
                    <div class="flex items-center mt-3">
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-green-100 text-green-700">
                            {{ number_format($purchaseStats['this_month_purchases']) }} this month
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Value Statistics Card -->
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-blue-700 uppercase tracking-wide">Total Value</p>
                        <div class="flex items-center space-x-1">
                            <div class="w-2 h-2 bg-blue-400 rounded-full"></div>
                            <div class="w-1 h-1 bg-blue-300 rounded-full"></div>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-blue-900 mt-1">PKR {{ number_format($valueStats['total_value'], 0) }}</p>
                    <div class="flex items-center mt-3">
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-700">
                            PKR {{ number_format($valueStats['this_month_value'], 0) }} this month
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Posted Purchases Statistics Card -->
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-emerald-700 uppercase tracking-wide">Posted</p>
                        <div class="flex items-center space-x-1">
                            <div class="w-2 h-2 bg-emerald-400 rounded-full"></div>
                            <div class="w-1 h-1 bg-emerald-300 rounded-full"></div>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-emerald-900 mt-1">{{ number_format($statusStats['posted']) }}</p>
                    <div class="flex items-center mt-3">
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-emerald-100 text-emerald-700">
                            Completed purchases
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Draft Purchases Statistics Card -->
        <div class="relative bg-gradient-to-br from-yellow-50 via-yellow-50 to-yellow-100 rounded-xl shadow-sm border border-yellow-100 p-6 overflow-hidden group hover:shadow-lg transition-all duration-300">
            <!-- Decorative elements -->
            <div class="absolute top-0 right-0 w-24 h-24 opacity-10">
                <svg viewBox="0 0 100 100" class="w-full h-full">
                    <circle cx="50" cy="50" r="40" fill="none" stroke="currentColor" stroke-width="1"/>
                    <circle cx="50" cy="50" r="25" fill="none" stroke="currentColor" stroke-width="1"/>
                    <circle cx="50" cy="50" r="10" fill="currentColor"/>
                </svg>
            </div>
            <div class="absolute bottom-0 left-0 w-16 h-16 bg-yellow-200 rounded-full -ml-8 -mb-8 opacity-20"></div>

            <div class="relative flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center shadow-md group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-yellow-700 uppercase tracking-wide">Draft</p>
                        <div class="flex items-center space-x-1">
                            <div class="w-2 h-2 bg-yellow-400 rounded-full"></div>
                            <div class="w-1 h-1 bg-yellow-300 rounded-full"></div>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-yellow-900 mt-1">{{ number_format($statusStats['draft']) }}</p>
                    <div class="flex items-center mt-3">
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-yellow-100 text-yellow-700">
                            Pending purchases
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Graphs Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Purchase Status Distribution Chart -->
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-500 transform hover:-translate-y-1">
            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-green-600 via-green-700 to-emerald-700 relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-green-600/80 to-emerald-700/80"></div>
                <div class="absolute -top-4 -right-4 w-24 h-24 bg-white/10 rounded-full"></div>
                <div class="absolute -bottom-2 -left-2 w-16 h-16 bg-white/5 rounded-full"></div>
                <div class="relative flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Purchase Status</h3>
                            <p class="text-green-100 text-sm">Purchase status distribution</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 bg-emerald-500 rounded-full"></div>
                            <span class="text-sm font-medium text-gray-700">Posted</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-32 bg-gray-200 rounded-full h-2">
                                @php
                                    $totalStatusPurchases = $statusStats['posted'] + $statusStats['draft'] + $statusStats['cancelled'];
                                    $postedPercentage = $totalStatusPurchases > 0 ? ($statusStats['posted'] / $totalStatusPurchases) * 100 : 0;
                                @endphp
                                <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $postedPercentage }}%"></div>
                            </div>
                            <span class="text-sm font-semibold text-gray-900">{{ $statusStats['posted'] }}</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 bg-yellow-500 rounded-full"></div>
                            <span class="text-sm font-medium text-gray-700">Draft</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-32 bg-gray-200 rounded-full h-2">
                                @php
                                    $draftPercentage = $totalStatusPurchases > 0 ? ($statusStats['draft'] / $totalStatusPurchases) * 100 : 0;
                                @endphp
                                <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ $draftPercentage }}%"></div>
                            </div>
                            <span class="text-sm font-semibold text-gray-900">{{ $statusStats['draft'] }}</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 bg-red-500 rounded-full"></div>
                            <span class="text-sm font-medium text-gray-700">Cancelled</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-32 bg-gray-200 rounded-full h-2">
                                @php
                                    $cancelledPercentage = $totalStatusPurchases > 0 ? ($statusStats['cancelled'] / $totalStatusPurchases) * 100 : 0;
                                @endphp
                                <div class="bg-red-500 h-2 rounded-full" style="width: {{ $cancelledPercentage }}%"></div>
                            </div>
                            <span class="text-sm font-semibold text-gray-900">{{ $statusStats['cancelled'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Types Chart -->
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-500 transform hover:-translate-y-1">
            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-600/80 to-indigo-700/80"></div>
                <div class="absolute -top-4 -right-4 w-24 h-24 bg-white/10 rounded-full"></div>
                <div class="absolute -bottom-2 -left-2 w-16 h-16 bg-white/5 rounded-full"></div>
                <div class="relative flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Payment Methods</h3>
                            <p class="text-blue-100 text-sm">Payment type distribution</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 bg-green-500 rounded-full"></div>
                            <span class="text-sm font-medium text-gray-700">Cash</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">{{ $paymentTypeStats['cash'] }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 bg-orange-500 rounded-full"></div>
                            <span class="text-sm font-medium text-gray-700">Credit</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">{{ $paymentTypeStats['credit'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 bg-purple-500 rounded-full"></div>
                            <span class="text-sm font-medium text-gray-700">Average Value</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">PKR {{ number_format($valueStats['average_value'], 0) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Parties Chart -->
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-500 transform hover:-translate-y-1">
            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-purple-600 via-purple-700 to-pink-700 relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-purple-600/80 to-pink-700/80"></div>
                <div class="absolute -top-4 -right-4 w-24 h-24 bg-white/10 rounded-full"></div>
                <div class="absolute -bottom-2 -left-2 w-16 h-16 bg-white/5 rounded-full"></div>
                <div class="relative flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Top Suppliers</h3>
                            <p class="text-purple-100 text-sm">Top {{ $partyStats['top_parties']->count() }} suppliers by value</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($partyStats['top_parties']->take(5) as $party)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 bg-purple-500 rounded-full"></div>
                            <span class="text-sm font-medium text-gray-700">{{ $party->party->name ?? 'Unknown Party' }}</span>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-semibold text-gray-900">PKR {{ number_format($party->total_value, 0) }}</div>
                            <div class="text-xs text-gray-500">{{ $party->purchase_count }} purchases</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Purchase Trends -->
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-500 transform hover:-translate-y-1">
            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-orange-600 via-orange-700 to-red-700 relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-orange-600/80 to-red-700/80"></div>
                <div class="absolute -top-4 -right-4 w-24 h-24 bg-white/10 rounded-full"></div>
                <div class="absolute -bottom-2 -left-2 w-16 h-16 bg-white/5 rounded-full"></div>
                <div class="relative flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Purchase Trends</h3>
                            <p class="text-orange-100 text-sm">Recent purchase activity</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 bg-orange-500 rounded-full"></div>
                            <span class="text-sm font-medium text-gray-700">Today</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">{{ $purchaseStats['today_purchases'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 bg-blue-500 rounded-full"></div>
                            <span class="text-sm font-medium text-gray-700">This Week</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">{{ $purchaseStats['this_week_purchases'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 bg-emerald-500 rounded-full"></div>
                            <span class="text-sm font-medium text-gray-700">This Month</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">{{ $purchaseStats['this_month_purchases'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 bg-purple-500 rounded-full"></div>
                            <span class="text-sm font-medium text-gray-700">Highest Value</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">PKR {{ number_format($valueStats['highest_value'], 0) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    @if($recentActivities['recent_purchases']->count() > 0)
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-500 transform hover:-translate-y-1 mb-8">
        <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-emerald-600 via-emerald-700 to-teal-700 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-emerald-600/80 to-teal-700/80"></div>
            <div class="absolute -top-6 -right-6 w-32 h-32 bg-white/10 rounded-full"></div>
            <div class="absolute -bottom-4 -left-4 w-20 h-20 bg-white/5 rounded-full"></div>
            <div class="relative flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Recent Purchases</h3>
                        <p class="text-emerald-100 text-sm">Latest purchase activities</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 bg-emerald-300 rounded-full animate-pulse"></div>
                    <div class="w-2 h-2 bg-emerald-300 rounded-full animate-pulse" style="animation-delay: 0.2s;"></div>
                    <div class="w-2 h-2 bg-emerald-300 rounded-full animate-pulse" style="animation-delay: 0.4s;"></div>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                @foreach($recentActivities['recent_purchases'] as $purchase)
                <div class="group relative p-3 bg-gradient-to-r from-gray-50 via-white to-gray-50 rounded-xl border border-gray-200 hover:border-green-300 hover:shadow-lg transition-all duration-300 cursor-pointer">
                    <div class="absolute inset-0 bg-gradient-to-r from-green-500/0 to-green-500/0 group-hover:from-green-500/5 group-hover:to-green-500/5 rounded-xl transition-all duration-300"></div>
                    <div class="relative flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-900 group-hover:text-green-700 transition-colors duration-200">Purchase #{{ $purchase->id }}</span>
                                <div class="text-xs text-gray-500">{{ $purchase->party->name ?? 'Cash Purchase' }} • PKR {{ number_format($purchase->total_amount) }}</div>
                                <div class="text-xs text-gray-400">{{ ucfirst($purchase->status) }} • {{ ucfirst($purchase->payment_type) }}</div>
                            </div>
                        </div>
                        <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded-full border border-gray-200">{{ $purchase->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Add smooth animations and interactive elements -->
    <script>
        // Business timezone from server
        const businessTimezone = '{{ $businessTimezone ?? "Asia/Karachi" }}';
        
        // Function to update time according to business timezone
        function updateBusinessTime() {
            const now = new Date();
            
            // Convert to business timezone
            const businessTime = new Date(now.toLocaleString("en-US", {timeZone: businessTimezone}));
            
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
        
        // Update time every second
        setInterval(updateBusinessTime, 1000);

        // Add smooth animations on load
        document.addEventListener('DOMContentLoaded', function() {
            // Animate statistics cards on load
            const statCards = document.querySelectorAll('.bg-gradient-to-br');
            statCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.6s ease-out';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });

            // Animate chart cards
            const chartCards = document.querySelectorAll('.hover\\:shadow-xl');
            chartCards.forEach((card, index) => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-4px) scale(1.02)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });

            // Animate progress indicators
            const progressIndicators = document.querySelectorAll('.animate-pulse');
            progressIndicators.forEach((indicator, index) => {
                indicator.style.animationDelay = `${index * 0.2}s`;
            });

            // Add click effects for interactive elements
            const interactiveElements = document.querySelectorAll('.cursor-pointer');
            interactiveElements.forEach(element => {
                element.addEventListener('click', function() {
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 150);
                });
            });
        });

        // Add keyboard navigation support
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                // Close any open modals or dropdowns
                const openElements = document.querySelectorAll('.open, .active');
                openElements.forEach(element => {
                    element.classList.remove('open', 'active');
                });
            }
        });

        // Add touch support for mobile devices
        if ('ontouchstart' in window) {
            document.querySelectorAll('.group').forEach(element => {
                element.addEventListener('touchstart', function() {
                    this.style.transform = 'scale(0.98)';
                });
                
                element.addEventListener('touchend', function() {
                    this.style.transform = 'scale(1)';
                });
            });
        }
    </script>
</x-app-layout>
