<x-app-layout>
    @section('title', 'General Items Dashboard - StoreBook')
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        ['url' => '/general-items-dashboard', 'label' => 'General Items Dashboard'],
    ]" />

    <!-- Header Section -->
    <div class="relative bg-gradient-to-r from-purple-50 via-white to-white rounded-xl shadow-sm border border-purple-100 p-6 mb-6 mt-4 overflow-hidden">
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
                <div class="flex-shrink-0 bg-gradient-to-br from-purple-500 to-purple-600 p-3 rounded-xl shadow-md">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 bg-gradient-to-r from-gray-900 to-purple-700 bg-clip-text text-transparent">
                        General Items Dashboard
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">Comprehensive overview of your general items inventory management</p>
                    <div class="flex items-center space-x-2 mt-1">
                        <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                        <span class="text-xs text-gray-500">General items system active</span>
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
                        <svg class="h-4 w-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-xs font-medium text-gray-700" id="current-time">{{ now()->setTimezone($businessTimezone ?? 'Asia/Karachi')->format('g:i A') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Total Items Statistics Card -->
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
                        <p class="text-sm font-semibold text-purple-700 uppercase tracking-wide">Total Items</p>
                        <div class="flex items-center space-x-1">
                            <div class="w-2 h-2 bg-purple-400 rounded-full"></div>
                            <div class="w-1 h-1 bg-purple-300 rounded-full"></div>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-purple-900 mt-1">{{ number_format($generalItemsStats['total_items']) }}</p>
                    <div class="flex items-center mt-3">
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-purple-100 text-purple-700">
                            {{ number_format($generalItemsStats['this_month_items']) }} this month
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Stock Statistics Card -->
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-blue-700 uppercase tracking-wide">Current Stock</p>
                        <div class="flex items-center space-x-1">
                            <div class="w-2 h-2 bg-blue-400 rounded-full"></div>
                            <div class="w-1 h-1 bg-blue-300 rounded-full"></div>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-blue-900 mt-1">{{ number_format($stockStats['current_stock']) }}</p>
                    <div class="flex items-center mt-3">
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-700">
                            {{ number_format($stockStats['active_batches']) }} active batches
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Value Statistics Card -->
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-emerald-700 uppercase tracking-wide">Total Value</p>
                        <div class="flex items-center space-x-1">
                            <div class="w-2 h-2 bg-emerald-400 rounded-full"></div>
                            <div class="w-1 h-1 bg-emerald-300 rounded-full"></div>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-emerald-900 mt-1">PKR {{ number_format($stockStats['current_stock_value'], 0) }}</p>
                    <div class="flex items-center mt-3">
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-emerald-100 text-emerald-700">
                            Current stock value
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Alerts Statistics Card -->
        <div class="relative bg-gradient-to-br from-red-50 via-red-50 to-red-100 rounded-xl shadow-sm border border-red-100 p-6 overflow-hidden group hover:shadow-lg transition-all duration-300">
            <!-- Decorative elements -->
            <div class="absolute top-0 right-0 w-24 h-24 opacity-10">
                <svg viewBox="0 0 100 100" class="w-full h-full">
                    <circle cx="50" cy="50" r="40" fill="none" stroke="currentColor" stroke-width="1"/>
                    <circle cx="50" cy="50" r="25" fill="none" stroke="currentColor" stroke-width="1"/>
                    <circle cx="50" cy="50" r="10" fill="currentColor"/>
                </svg>
            </div>
            <div class="absolute bottom-0 left-0 w-16 h-16 bg-red-200 rounded-full -ml-8 -mb-8 opacity-20"></div>

            <div class="relative flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shadow-md group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-red-700 uppercase tracking-wide">Low Stock</p>
                        <div class="flex items-center space-x-1">
                            <div class="w-2 h-2 bg-red-400 rounded-full"></div>
                            <div class="w-1 h-1 bg-red-300 rounded-full"></div>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-red-900 mt-1">{{ number_format($generalItemsStats['low_stock_items']) }}</p>
                    <div class="flex items-center mt-3">
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-red-100 text-red-700">
                            Items need attention
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions (moved below statistics cards, layout corrected) -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:flex-wrap gap-4">
            @can('create items')
            <a href="{{ route('general-items.create') }}"
                class="group flex-1 min-w-[220px] bg-gradient-to-r from-white to-purple-50/30 rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-lg hover:border-purple-200 transition-all duration-300 flex items-center space-x-3 hover:-translate-y-0.5">
                <div class="flex-shrink-0 bg-gradient-to-br from-purple-500 to-purple-600 p-2.5 rounded-xl shadow-md group-hover:shadow-lg group-hover:scale-105 transition-all duration-300">
                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-gray-900 group-hover:text-purple-700 transition-colors duration-200">Add New Item</h3>
                    <p class="text-xs text-gray-500 group-hover:text-gray-600 transition-colors duration-200">Create new general item</p>
                </div>
                <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <svg class="h-4 w-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </a>
            @endcan
            @can('view items')
            <a href="{{ route('general-items.index') }}"
                class="group flex-1 min-w-[220px] bg-gradient-to-r from-white to-blue-50/30 rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-lg hover:border-blue-200 transition-all duration-300 flex items-center space-x-3 hover:-translate-y-0.5">
                <div class="flex-shrink-0 bg-gradient-to-br from-blue-500 to-blue-600 p-2.5 rounded-xl shadow-md group-hover:shadow-lg group-hover:scale-105 transition-all duration-300">
                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-gray-900 group-hover:text-blue-700 transition-colors duration-200">All Items</h3>
                    <p class="text-xs text-gray-500 group-hover:text-gray-600 transition-colors duration-200">View all general items</p>
                </div>
                <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <svg class="h-4 w-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </a>

            <!-- <a href="{{ route('general-items-stock-ledger') }}"
                class="group flex-1 min-w-[220px] bg-gradient-to-r from-white to-emerald-50/30 rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-lg hover:border-emerald-200 transition-all duration-300 flex items-center space-x-3 hover:-translate-y-0.5">
                <div class="flex-shrink-0 bg-gradient-to-br from-emerald-500 to-emerald-600 p-2.5 rounded-xl shadow-md group-hover:shadow-lg group-hover:scale-105 transition-all duration-300">
                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-gray-900 group-hover:text-emerald-700 transition-colors duration-200">Stock Ledger</h3>
                    <p class="text-xs text-gray-500 group-hover:text-gray-600 transition-colors duration-200">Items stock reports</p>
                </div>
                <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <svg class="h-4 w-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </a> -->

            <a href="{{ route('general-items.inventory-valuation-summary') }}"
                class="group flex-1 min-w-[220px] bg-gradient-to-r from-white to-amber-50/30 rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-lg hover:border-amber-200 transition-all duration-300 flex items-center space-x-3 hover:-translate-y-0.5">
                <div class="flex-shrink-0 bg-gradient-to-br from-amber-500 to-amber-600 p-2.5 rounded-xl shadow-md group-hover:shadow-lg group-hover:scale-105 transition-all duration-300">
                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-gray-900 group-hover:text-amber-700 transition-colors duration-200">Inventory Valuation</h3>
                    <p class="text-xs text-gray-500 group-hover:text-gray-600 transition-colors duration-200">Summary report</p>
                </div>
                <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <svg class="h-4 w-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </a>

            <a href="{{ route('stock-adjustments.index') }}"
                class="group flex-1 min-w-[220px] bg-gradient-to-r from-white to-green-50/30 rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-lg hover:border-green-200 transition-all duration-300 flex items-center space-x-3 hover:-translate-y-0.5">
                <div class="flex-shrink-0 bg-gradient-to-br from-green-500 to-green-600 p-2.5 rounded-xl shadow-md group-hover:shadow-lg group-hover:scale-105 transition-all duration-300">
                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-gray-900 group-hover:text-green-700 transition-colors duration-200">Stock Adjustments</h3>
                    <p class="text-xs text-gray-500 group-hover:text-gray-600 transition-colors duration-200">Add or subtract inventory</p>
                </div>
                <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <svg class="h-4 w-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </a>
            @endcan
            @can('view item-types')
            <a href="{{ route('item-types.index') }}"
                class="group flex-1 min-w-[220px] bg-gradient-to-r from-white to-indigo-50/30 rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-lg hover:border-indigo-200 transition-all duration-300 flex items-center space-x-3 hover:-translate-y-0.5">
                <div class="flex-shrink-0 bg-gradient-to-br from-indigo-500 to-indigo-600 p-2.5 rounded-xl shadow-md group-hover:shadow-lg group-hover:scale-105 transition-all duration-300">
                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-gray-900 group-hover:text-indigo-700 transition-colors duration-200">Item Types</h3>
                    <p class="text-xs text-gray-500 group-hover:text-gray-600 transition-colors duration-200">Manage item categories</p>
                </div>
                <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <svg class="h-4 w-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </a>
        </div>
    </div>
    @endcan
    <!-- Charts and Graphs Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Item Types Distribution Chart -->
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-500 transform hover:-translate-y-1">
            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-purple-600 via-purple-700 to-pink-700 relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-purple-600/80 to-pink-700/80"></div>
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
                            <h3 class="text-xl font-bold text-white">Item Types Distribution</h3>
                            <p class="text-purple-100 text-sm">Top {{ $itemTypesStats['top_types']->count() }} item types by count</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($itemTypesStats['top_types']->take(5) as $type)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 bg-purple-500 rounded-full"></div>
                            <span class="text-sm font-medium text-gray-700">{{ $type->item_type }}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-32 bg-gray-200 rounded-full h-2">
                                <div class="bg-purple-500 h-2 rounded-full" style="width: {{ $generalItemsStats['total_items'] > 0 ? ($type->count / $generalItemsStats['total_items']) * 100 : 0 }}%"></div>
                            </div>
                            <span class="text-sm font-semibold text-gray-900">{{ $type->count }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Stock Movement Chart -->
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-500 transform hover:-translate-y-1">
            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-emerald-600 via-emerald-700 to-teal-700 relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-emerald-600/80 to-teal-700/80"></div>
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
                            <h3 class="text-xl font-bold text-white">Stock Movement</h3>
                            <p class="text-emerald-100 text-sm">Stock in vs stock out overview</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 bg-emerald-500 rounded-full"></div>
                            <span class="text-sm font-medium text-gray-700">Total Received</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">{{ number_format($stockStats['total_received']) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 bg-blue-500 rounded-full"></div>
                            <span class="text-sm font-medium text-gray-700">Current Stock</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">{{ number_format($stockStats['current_stock']) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 bg-orange-500 rounded-full"></div>
                            <span class="text-sm font-medium text-gray-700">Total Consumed</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">{{ number_format($stockStats['total_consumed']) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 bg-purple-500 rounded-full"></div>
                            <span class="text-sm font-medium text-gray-700">Active Batches</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">{{ number_format($stockStats['active_batches']) }}</span>
                    </div>
                </div>
            </div>
        </div>



        <!-- Batch Information Chart -->
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-500 transform hover:-translate-y-1">
            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-orange-600 via-orange-700 to-red-700 relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-orange-600/80 to-red-700/80"></div>
                <div class="absolute -top-4 -right-4 w-24 h-24 bg-white/10 rounded-full"></div>
                <div class="absolute -bottom-2 -left-2 w-16 h-16 bg-white/5 rounded-full"></div>
                <div class="relative flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Batch Summary</h3>
                            <p class="text-orange-100 text-sm">Batch statistics overview</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 bg-orange-500 rounded-full"></div>
                            <span class="text-sm font-medium text-gray-700">Total Batches</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">{{ number_format($batchStats['total_batches']) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 bg-emerald-500 rounded-full"></div>
                            <span class="text-sm font-medium text-gray-700">Active Batches</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">{{ number_format($batchStats['active_batches']) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 bg-red-500 rounded-full"></div>
                            <span class="text-sm font-medium text-gray-700">Reversed Batches</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">{{ number_format($batchStats['reversed_batches']) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 bg-blue-500 rounded-full"></div>
                            <span class="text-sm font-medium text-gray-700">Total Batch Value</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">PKR {{ number_format($batchStats['total_batch_value'], 0) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 gap-6">
        <!-- Content Area -->
            <!-- Enhanced Low Stock Alerts -->
            @if($lowStockAlerts['low_stock_items']->count() > 0)
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-500 transform hover:-translate-y-1">
                <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-red-600 via-red-700 to-pink-700 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-red-600/80 to-pink-700/80"></div>
                    <div class="absolute -top-4 -right-4 w-24 h-24 bg-white/10 rounded-full"></div>
                    <div class="absolute -bottom-2 -left-2 w-16 h-16 bg-white/5 rounded-full"></div>
                    <div class="relative flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center shadow-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white">Low Stock Alerts</h3>
                                <p class="text-red-100 text-sm">Items requiring attention</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 bg-red-300 rounded-full animate-pulse"></div>
                            <div class="w-2 h-2 bg-red-300 rounded-full animate-pulse" style="animation-delay: 0.2s;"></div>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-2">
                        @foreach($lowStockAlerts['low_stock_items']->take(8) as $item)
                        <div class="group relative p-3 bg-gradient-to-r from-red-50 via-white to-red-50 rounded-xl border border-red-200 hover:border-red-300 hover:shadow-md transition-all duration-300">
                            <div class="absolute inset-0 bg-gradient-to-r from-red-500/0 to-red-500/0 group-hover:from-red-500/5 group-hover:to-red-500/5 rounded-xl transition-all duration-300"></div>
                            <div class="relative flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-900 group-hover:text-red-700 transition-colors duration-200">{{ $item->item_name }}</span>
                                        <div class="text-xs text-gray-500">{{ $item->itemType->item_type ?? 'No Type' }}</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs text-red-600 font-semibold bg-red-100 px-2 py-1 rounded-full border border-red-200">{{ $item->current_stock }}</span>
                                    @if($item->min_stock_limit)
                                    <div class="text-xs text-gray-500 mt-1">Min: {{ $item->min_stock_limit }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
    </div>

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
