<x-app-layout>
    @section('title', $arm->arm_title . ' - Arms Details - Arms Management')
    
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'],['url' => '/arms-dashboard', 'label' => 'Arms Dashboard'],['url' => '#', 'label' => $arm->arm_title]]" />

    <!-- Header Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6 mt-5">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-orange-600 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $arm->arm_title }}</h1>
                            <p class="text-sm text-gray-500 font-mono">Serial: {{ $arm->serial_no }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex gap-3">
                    @if(is_null($arm->purchase_id) && $arm->status === 'available')
                        <a href="{{ route('arms.edit', $arm) }}" 
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit Arm
                        </a>
                    @else
                        <span class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-lg font-medium text-sm text-gray-500 cursor-not-allowed" title="Arm received via purchase cannot be edited or deleted.">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
                            </svg>
                            Cannot Edit
                        </span>
                    @endif
                    <a href="{{ route('arms.index') }}" 
                        class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg font-medium text-sm text-gray-700 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to List
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Status Bar -->
        <div class="px-6 py-4 bg-gray-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-6">
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-medium text-gray-700">Status:</span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                            {{ $arm->status === 'available' ? 'bg-green-100 text-green-800' : 
                               ($arm->status === 'sold' ? 'bg-blue-100 text-blue-800' : 
                               ($arm->status === 'decommissioned' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) }}">
                            {{ ucfirst(str_replace('_', ' ', $arm->status)) }}
                        </span>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-medium text-gray-700">Serial Number:</span>
                        <span class="text-lg font-bold text-gray-900 font-mono">{{ $arm->serial_no }}</span>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Cost Price</p>
                        <p class="text-lg font-bold text-gray-900">PKR {{ number_format($arm->purchase_price, 2) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Sale Price</p>
                        <p class="text-lg font-bold text-green-600">PKR {{ number_format($arm->sale_price, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Main Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Specifications Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                        Technical Specifications
                            </h3>
                </div>
                <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-4">
                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                <span class="text-sm font-medium text-gray-600">Arm Type</span>
                                        <span class="text-sm text-gray-900 font-medium">{{ $arm->armType->name }}</span>
                                    </div>
                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                        <span class="text-sm font-medium text-gray-600">Category</span>
                                        <span class="text-sm text-gray-900 font-medium">{{ $arm->armCategory->name }}</span>
                                    </div>
                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                        <span class="text-sm font-medium text-gray-600">Make</span>
                                        <span class="text-sm text-gray-900 font-medium">{{ $arm->armMake->arm_make ?? 'N/A' }}</span>
                                    </div>
                                </div>
                        
                                <div class="space-y-4">
                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                        <span class="text-sm font-medium text-gray-600">Caliber</span>
                                        <span class="text-sm text-gray-900 font-medium">{{ $arm->armCaliber->name }}</span>
                                    </div>
                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                        <span class="text-sm font-medium text-gray-600">Condition</span>
                                        <span class="text-sm text-gray-900 font-medium">{{ $arm->armCondition->name }}</span>
                                    </div>
                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                        <span class="text-sm font-medium text-gray-600">Serial Number</span>
                                        <span class="text-sm text-gray-900 font-mono font-medium">{{ $arm->serial_no }}</span>
                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>

            <!-- Financial Information Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                </svg>
                        Financial Details
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <label class="block text-sm font-medium text-blue-600 mb-2">Cost Price</label>
                            <p class="text-2xl font-bold text-blue-900">PKR {{ number_format($arm->purchase_price, 2) }}</p>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <label class="block text-sm font-medium text-green-600 mb-2">Sale Price</label>
                            <p class="text-2xl font-bold text-green-900">PKR {{ number_format($arm->sale_price, 2) }}</p>
                        </div>
                        <div class="text-center p-4 bg-purple-50 rounded-lg">
                            <label class="block text-sm font-medium text-purple-600 mb-2">Profit Margin</label>
                            <p class="text-2xl font-bold text-purple-900">PKR {{ number_format($arm->sale_price - $arm->purchase_price, 2) }}</p>
                        </div>
                    </div>
                    
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 bg-yellow-50 rounded-lg">
                            <label class="block text-sm font-medium text-yellow-600 mb-1">Margin Percentage</label>
                            <p class="text-xl font-semibold text-yellow-900">{{ number_format((($arm->sale_price - $arm->purchase_price) / $arm->purchase_price) * 100, 1) }}%</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <label class="block text-sm font-medium text-gray-600 mb-1">Opening Date</label>
                            <p class="text-xl font-semibold text-gray-900">{{ $arm->purchase_date->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Opening Stock Information Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                                Opening Stock Information
                            </h3>
                </div>
                <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-4">
                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                        <span class="text-sm font-medium text-gray-600">Cost Price</span>
                                        <span class="text-sm font-bold text-gray-900">PKR {{ number_format($arm->purchase_price, 2) }}</span>
                                    </div>
                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                        <span class="text-sm font-medium text-gray-600">Opening Date</span>
                                        <span class="text-sm text-gray-900 font-medium">{{ $arm->purchase_date->format('M d, Y') }}</span>
                                    </div>
                            @if($arm->purchase_id)
                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                <span class="text-sm font-medium text-gray-600">Purchase ID</span>
                                <span class="text-sm text-gray-900 font-mono">#{{ $arm->purchase_id }}</span>
                            </div>
                            @endif
                                </div>
                        
                                <div class="space-y-4">
                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                        <span class="text-sm font-medium text-gray-600">Current Status</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $arm->status === 'available' ? 'bg-green-100 text-green-800' : 
                                       ($arm->status === 'sold' ? 'bg-blue-100 text-blue-800' : 
                                       ($arm->status === 'decommissioned' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                            {{ ucfirst(str_replace('_', ' ', $arm->status)) }}
                                        </span>
                                    </div>
                                    @if($arm->notes)
                            <div class="flex justify-between items-start py-3 border-b border-gray-100">
                                        <span class="text-sm font-medium text-gray-600">Notes</span>
                                        <span class="text-sm text-gray-900 max-w-xs text-right">{{ $arm->notes }}</span>
                                    </div>
                                    @endif
                            @if($arm->purchase_arm_serial_id)
                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                <span class="text-sm font-medium text-gray-600">Serial ID</span>
                                <span class="text-sm text-gray-900 font-mono">#{{ $arm->purchase_arm_serial_id }}</span>
                            </div>
                            @endif
                            </div>
                        </div>
                    </div>
                </div>

            <!-- Additional Information Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200" x-data="{ additionalInfoOpen: false }">
                <div class="px-6 py-4 border-b border-gray-200">
                    <button @click="additionalInfoOpen = !additionalInfoOpen" class="flex items-center justify-between w-full text-left">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Additional Information
                        </h3>
                        <svg class="w-5 h-5 text-gray-600 transition-transform duration-200" :class="{ 'rotate-180': additionalInfoOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </div>
                
                <div x-show="additionalInfoOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2">
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="text-sm font-medium text-gray-600">Business</span>
                                    <span class="text-sm text-gray-900">{{ $arm->business->business_name }}</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="text-sm font-medium text-gray-600">Created At</span>
                                    <span class="text-sm text-gray-900">{{ $arm->created_at->format('M d, Y h:i A') }}</span>
                                </div>
                            </div>
                            <div class="space-y-4">
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-sm font-medium text-gray-600">Last Updated</span>
                                    <span class="text-sm text-gray-900">{{ $arm->updated_at->format('M d, Y h:i A') }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="text-sm font-medium text-gray-600">ID</span>
                                    <span class="text-sm text-gray-900 font-mono">#{{ $arm->id }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <!-- Right Column - Sidebar -->
        <div class="space-y-6">
            <!-- Status Overview Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                        </svg>
                        Status Overview
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-gray-900 mb-2">{{ ucfirst(str_replace('_', ' ', $arm->status)) }}</div>
                            <div class="text-sm text-gray-600">Current Status</div>
        </div>
        
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Status</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $arm->status === 'available' ? 'bg-green-100 text-green-800' : 
                                       ($arm->status === 'sold' ? 'bg-blue-100 text-blue-800' : 
                                       ($arm->status === 'decommissioned' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                    {{ ucfirst(str_replace('_', ' ', $arm->status)) }}
                                </span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Type</span>
                                <span class="text-sm font-medium text-gray-900">{{ $arm->armType->name }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Category</span>
                                <span class="text-sm font-medium text-gray-900">{{ $arm->armCategory->name }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Make</span>
                                <span class="text-sm font-medium text-gray-900">{{ $arm->armMake->arm_make ?? 'N/A' }}</span>
            </div>
                </div>
            </div>
        </div>
    </div>

            <!-- Quick Actions Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Quick Actions
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        @if(is_null($arm->purchase_id) && $arm->status === 'available')
                            <a href="{{ route('arms.edit', $arm) }}" 
                                class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit Arm
                            </a>
                        @else
                            <span class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-gray-500 bg-gray-300 cursor-not-allowed" title="Arm received via purchase cannot be edited or deleted.">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
                                </svg>
                                Cannot Edit
                            </span>
                        @endif
                        
                        <a href="{{ route('arms.index') }}" 
                            class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            View All Arms
                        </a>
                    </div>
                </div>
            </div>

            <!-- Financial Summary Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Financial Summary
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="text-center p-3 bg-green-50 rounded-lg">
                            <div class="text-lg font-bold text-green-900">PKR {{ number_format($arm->sale_price - $arm->purchase_price, 2) }}</div>
                            <div class="text-sm text-green-600">Profit Margin</div>
        </div>
        
                        <div class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Cost:</span>
                                <span class="text-gray-900">PKR {{ number_format($arm->purchase_price, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Sale:</span>
                                <span class="text-green-600 font-medium">PKR {{ number_format($arm->sale_price, 2) }}</span>
                            </div>
                            <div class="border-t pt-2">
                                <div class="flex justify-between text-sm font-medium">
                                    <span class="text-gray-600">Margin %:</span>
                                    <span class="text-green-600">{{ number_format((($arm->sale_price - $arm->purchase_price) / $arm->purchase_price) * 100, 1) }}%</span>
                                </div>
                            </div>
                                        </div>
            </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
