<x-app-layout>
    @section('title', 'Arms Dashboard - Arms Management')
    
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'], ['url' => '/arms-dashboard', 'label' => 'Arms Dashboard']]" />

    <!-- Header Section -->
    <div class="bg-gradient-to-r from-green-50 via-white to-white rounded-xl shadow-sm border border-green-100 p-4 sm:p-5 mb-4 mt-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Arms Dashboard</h1>
                    <p class="text-sm text-gray-500 mt-1">Manage your organization's arms inventory and related settings</p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                <span class="bg-indigo-50 px-3 py-1 rounded-full border border-indigo-100 text-sm text-gray-500 flex items-center">
                    <svg class="w-4 h-4 mr-1 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Arms Dashboard
                </span>
                <div class="flex items-center space-x-2">
                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Active</span>
                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">{{ now()->format('d M Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
        <!-- Total Arms -->
        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300 transform hover:-translate-y-0.5">
            <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-600/80 to-indigo-700/80"></div>
                <div class="absolute -top-3 -right-3 w-16 h-16 bg-white/10 rounded-full"></div>
                <div class="absolute -bottom-1 -left-1 w-12 h-12 bg-white/5 rounded-full"></div>
                <div class="relative flex items-center justify-between">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center shadow-md">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-white leading-tight">Total Arms</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600 mb-1">{{ $armsStats['total'] }}</div>
                    <div class="text-xs text-gray-500">All arms in inventory</div>
                </div>
            </div>
        </div>

        <!-- Available Arms -->
        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300 transform hover:-translate-y-0.5">
            <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-green-600 via-green-700 to-emerald-700 relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-green-600/80 to-emerald-700/80"></div>
                <div class="absolute -top-3 -right-3 w-16 h-16 bg-white/10 rounded-full"></div>
                <div class="absolute -bottom-1 -left-1 w-12 h-12 bg-white/5 rounded-full"></div>
                <div class="relative flex items-center justify-between">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center shadow-md">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-white leading-tight">Available Arms</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600 mb-1">{{ $armsStats['available'] }}</div>
                    <div class="text-xs text-gray-500">Available for use</div>
                </div>
            </div>
        </div>

        <!-- Purchase Arms -->
        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300 transform hover:-translate-y-0.5">
            <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-emerald-600 via-emerald-700 to-teal-700 relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-emerald-600/80 to-teal-700/80"></div>
                <div class="absolute -top-3 -right-3 w-16 h-16 bg-white/10 rounded-full"></div>
                <div class="absolute -bottom-1 -left-1 w-12 h-12 bg-white/5 rounded-full"></div>
                <div class="relative flex items-center justify-between">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center shadow-md">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m6 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-white leading-tight">Purchase Arms</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-emerald-600 mb-1">{{ $armsStats['purchased'] }}</div>
                    <div class="text-xs text-gray-500">PKR {{ number_format($armsStats['purchased_value'], 0) }}</div>
                </div>
            </div>
        </div>

        <!-- Total Value -->
        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300 transform hover:-translate-y-0.5">
            <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-orange-600 via-orange-700 to-red-700 relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-orange-600/80 to-red-700/80"></div>
                <div class="absolute -top-3 -right-3 w-16 h-16 bg-white/10 rounded-full"></div>
                <div class="absolute -bottom-1 -left-1 w-12 h-12 bg-white/5 rounded-full"></div>
                <div class="relative flex items-center justify-between">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center shadow-md">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-white leading-tight">Total Value</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-orange-600 mb-1">PKR {{ number_format($armsStats['total_value'], 0) }}</div>
                    <div class="text-xs text-gray-500">Total inventory value</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
       
    <!-- Add New Arm -->
    
        <!-- Opening Stock Arms -->
        @can('view arms opening stock')
        <a href="{{ route('arms.opening-stock') }}"
            class="group bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-lg hover:border-orange-200 transition-all duration-300 transform hover:-translate-y-1 flex items-center space-x-3 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-orange-50 to-orange-100 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="flex-shrink-0 bg-gradient-to-br from-orange-500 to-orange-600 p-2.5 rounded-lg group-hover:scale-110 transition-transform duration-300 relative z-10">
                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
            <div class="relative z-10">
                <h3 class="text-sm font-semibold text-gray-900 group-hover:text-orange-600 transition-colors duration-300">Opening Stock</h3>
                <p class="text-xs text-gray-500 group-hover:text-orange-600 transition-colors duration-300">Manage opening stock arms</p>
            </div>
        </a>
        @endcan

        @can('view arm_types')
        <a href="{{ url('arms-types') }}"
            class="group bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-lg hover:border-blue-200 transition-all duration-300 transform hover:-translate-y-1 flex items-center space-x-3 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-blue-100 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="flex-shrink-0 bg-gradient-to-br from-blue-500 to-blue-600 p-2.5 rounded-lg group-hover:scale-110 transition-transform duration-300 relative z-10">
                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <div class="relative z-10">
                <h3 class="text-sm font-semibold text-gray-900 group-hover:text-blue-700 transition-colors duration-300">Arm Types</h3>
                <p class="text-xs text-gray-500 group-hover:text-blue-600 transition-colors duration-300">Manage arm types</p>
            </div>
        </a>
        @endcan

        @can('view arm_categories')
        <a href="{{ url('arms-categories') }}"
            class="group bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-lg hover:border-green-200 transition-all duration-300 transform hover:-translate-y-1 flex items-center space-x-3 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-green-100 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="flex-shrink-0 bg-gradient-to-br from-green-500 to-green-600 p-2.5 rounded-lg group-hover:scale-110 transition-transform duration-300 relative z-10">
                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
            </div>
            <div class="relative z-10">
                <h3 class="text-sm font-semibold text-gray-900 group-hover:text-green-700 transition-colors duration-300">Arm Categories</h3>
                <p class="text-xs text-gray-500 group-hover:text-green-600 transition-colors duration-300">Manage arm categories</p>
            </div>
        </a>
        @endcan
         
        @can('view arm_makes')
        <a href="{{ url('arms-makes') }}"
            class="group bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-lg hover:border-purple-200 transition-all duration-300 transform hover:-translate-y-1 flex items-center space-x-3 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-50 to-purple-100 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="flex-shrink-0 bg-gradient-to-br from-purple-500 to-purple-600 p-2.5 rounded-lg group-hover:scale-110 transition-transform duration-300 relative z-10">
                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div class="relative z-10">
                <h3 class="text-sm font-semibold text-gray-900 group-hover:text-purple-700 transition-colors duration-300">Arm Makes</h3>
                <p class="text-xs text-gray-500 group-hover:text-purple-600 transition-colors duration-300">Manage arm makes</p>
            </div>
        </a>
        @endcan
        
        @can('view arm_calibers')
        <a href="{{ url('arms-calibers') }}"
            class="group bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-lg hover:border-yellow-200 transition-all duration-300 transform hover:-translate-y-1 flex items-center space-x-3 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-yellow-50 to-yellow-100 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="flex-shrink-0 bg-gradient-to-br from-yellow-500 to-yellow-600 p-2.5 rounded-lg group-hover:scale-110 transition-transform duration-300 relative z-10">
                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <div class="relative z-10">
                <h3 class="text-sm font-semibold text-gray-900 group-hover:text-yellow-700 transition-colors duration-300">Arm Calibers</h3>
                <p class="text-xs text-gray-500 group-hover:text-yellow-600 transition-colors duration-300">Manage arm calibers</p>
            </div>
        </a>
        @endcan

        @can('view arm_conditions')
        <a href="{{ url('arms-conditions') }}"
            class="group bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-lg hover:border-red-200 transition-all duration-300 transform hover:-translate-y-1 flex items-center space-x-3 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-red-50 to-red-100 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="flex-shrink-0 bg-gradient-to-br from-red-500 to-red-600 p-2.5 rounded-lg group-hover:scale-110 transition-transform duration-300 relative z-10">
                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="relative z-10">
                <h3 class="text-sm font-semibold text-gray-900 group-hover:text-red-700 transition-colors duration-300">Arm Conditions</h3>
                <p class="text-xs text-gray-500 group-hover:text-red-600 transition-colors duration-300">Manage arm conditions</p>
            </div>
        </a>
        @endcan

        

        

        <!-- Arms Report -->
        @can('view arms report')
        <a href="{{ route('arms.report') }}"
            class="group bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-lg hover:border-blue-200 transition-all duration-300 transform hover:-translate-y-1 flex items-center space-x-3 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-blue-100 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="flex-shrink-0 bg-gradient-to-br from-blue-500 to-blue-600 p-2.5 rounded-lg group-hover:scale-110 transition-transform duration-300 relative z-10">
                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div class="relative z-10">
                <h3 class="text-sm font-semibold text-gray-900 group-hover:text-blue-700 transition-colors duration-300">Arms Report</h3>
                <p class="text-xs text-gray-500 group-hover:text-blue-600 transition-colors duration-300">Generate arms report</p>
            </div>
        </a>
        @endcan

        @can('view arms history')
        <!-- Arm History -->
        <a href="{{ route('arms-history') }}"
            class="group bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-lg hover:border-purple-200 transition-all duration-300 transform hover:-translate-y-1 flex items-center space-x-3 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-50 to-purple-100 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="flex-shrink-0 bg-gradient-to-br from-purple-500 to-purple-600 p-2.5 rounded-lg group-hover:scale-110 transition-transform duration-300 relative z-10">
                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="relative z-10">
                <h3 class="text-sm font-semibold text-gray-900 group-hover:text-purple-700 transition-colors duration-300">Arm History</h3>
                <p class="text-xs text-gray-500 group-hover:text-purple-600 transition-colors duration-300">View arm history</p>
            </div>
        </a>
        @endcan

        <!-- @can('view arms stock ledger')
        <a href="{{ route('arms-stock-ledger') }}"
            class="group bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-lg hover:border-teal-200 transition-all duration-300 transform hover:-translate-y-1 flex items-center space-x-3 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-teal-50 to-teal-100 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="flex-shrink-0 bg-gradient-to-br from-teal-500 to-teal-600 p-2.5 rounded-lg group-hover:scale-110 transition-transform duration-300 relative z-10">
                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <div class="relative z-10">
                <h3 class="text-sm font-semibold text-gray-900 group-hover:text-teal-700 transition-colors duration-300">Stock Ledger</h3>
                <p class="text-xs text-gray-500 group-hover:text-teal-600 transition-colors duration-300">View stock ledger</p>
            </div>
        </a>
        @endcan -->
       
    </div>

    <!-- Charts and Graphs Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Arms Status Distribution Chart -->
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-500 transform hover:-translate-y-1">
            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-600/80 to-indigo-700/80"></div>
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
                            <h3 class="text-xl font-bold text-white">Arms Status Distribution</h3>
                            <p class="text-blue-100 text-sm">Current status breakdown</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <!-- Available Arms -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 bg-green-500 rounded-full"></div>
                            <span class="text-sm font-medium text-gray-700">Available</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-32 bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: {{ $armsStats['total'] > 0 ? ($armsStats['available'] / $armsStats['total']) * 100 : 0 }}%"></div>
                            </div>
                            <span class="text-sm font-semibold text-gray-900">{{ $armsStats['available'] }}</span>
                        </div>
                    </div>
                    
                    <!-- Sold Arms -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 bg-blue-500 rounded-full"></div>
                            <span class="text-sm font-medium text-gray-700">Sold</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-32 bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $armsStats['total'] > 0 ? ($armsStats['sold'] / $armsStats['total']) * 100 : 0 }}%"></div>
                            </div>
                            <span class="text-sm font-semibold text-gray-900">{{ $armsStats['sold'] }}</span>
                        </div>
                    </div>
                    
                    <!-- Under Repair -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 bg-yellow-500 rounded-full"></div>
                            <span class="text-sm font-medium text-gray-700">Under Repair</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-32 bg-gray-200 rounded-full h-2">
                                <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ $armsStats['total'] > 0 ? ($armsStats['under_repair'] / $armsStats['total']) * 100 : 0 }}%"></div>
                            </div>
                            <span class="text-sm font-semibold text-gray-900">{{ $armsStats['under_repair'] }}</span>
                        </div>
                    </div>
                    
                    <!-- Decommissioned -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 bg-gray-500 rounded-full"></div>
                            <span class="text-sm font-medium text-gray-700">Decommissioned</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-32 bg-gray-200 rounded-full h-2">
                                <div class="bg-gray-500 h-2 rounded-full" style="width: {{ $armsStats['total'] > 0 ? ($armsStats['decommissioned'] / $armsStats['total']) * 100 : 0 }}%"></div>
                            </div>
                            <span class="text-sm font-semibold text-gray-900">{{ $armsStats['decommissioned'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Arms Types Distribution Chart -->
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-500 transform hover:-translate-y-1">
            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-purple-600 via-purple-700 to-pink-700 relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-purple-600/80 to-pink-700/80"></div>
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
                            <h3 class="text-xl font-bold text-white">Top Arm Types</h3>
                            <p class="text-purple-100 text-sm">Most common arm types</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($armsTypesStats['top_types']->take(5) as $type)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 bg-purple-500 rounded-full"></div>
                            <span class="text-sm font-medium text-gray-700">{{ $type->arm_type }}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-32 bg-gray-200 rounded-full h-2">
                                <div class="bg-purple-500 h-2 rounded-full" style="width: {{ $armsStats['total'] > 0 ? ($type->count / $armsStats['total']) * 100 : 0 }}%"></div>
                            </div>
                            <span class="text-sm font-semibold text-gray-900">{{ $type->count }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Monthly Arms Trend Chart -->
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-500 transform hover:-translate-y-1">
            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-emerald-600 via-emerald-700 to-teal-700 relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-emerald-600/80 to-teal-700/80"></div>
                <div class="absolute -top-4 -right-4 w-24 h-24 bg-white/10 rounded-full"></div>
                <div class="absolute -bottom-2 -left-2 w-16 h-16 bg-white/5 rounded-full"></div>
                <div class="relative flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3m0 0l-3 3m3-3H21" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Monthly Trend</h3>
                            <p class="text-emerald-100 text-sm">Arms added this month</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-emerald-600 mb-2">{{ $armsStats['this_month'] }}</div>
                    <div class="text-sm text-gray-600">Arms added in {{ now()->format('F Y') }}</div>
                    <div class="mt-4">
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-emerald-500 h-3 rounded-full" style="width: {{ $armsStats['this_month'] > 0 ? min(($armsStats['this_month'] / max($armsStats['total'], 1)) * 100, 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Value Distribution Chart -->
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-500 transform hover:-translate-y-1">
            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-orange-600 via-orange-700 to-red-700 relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-orange-600/80 to-red-700/80"></div>
                <div class="absolute -top-4 -right-4 w-24 h-24 bg-white/10 rounded-full"></div>
                <div class="absolute -bottom-2 -left-2 w-16 h-16 bg-white/5 rounded-full"></div>
                <div class="relative flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Value Distribution</h3>
                            <p class="text-orange-100 text-sm">Total inventory value</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <!-- Available Value (Total) -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 bg-orange-500 rounded-full"></div>
                            <span class="text-sm font-medium text-gray-700">Available Value</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">PKR {{ number_format($armsStats['total_value'], 0) }}</span>
                    </div>
                    
                    
                    <!-- Sold Value -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 bg-blue-500 rounded-full"></div>
                            <span class="text-sm font-medium text-gray-700">Sold Value</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">PKR {{ number_format($armsStats['sold_value'], 0) }}</span>
                    </div>
                    
                    <!-- Purchase Value -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 bg-emerald-500 rounded-full"></div>
                            <span class="text-sm font-medium text-gray-700">Purchase Value</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">PKR {{ number_format($armsStats['purchased_value'], 0) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 