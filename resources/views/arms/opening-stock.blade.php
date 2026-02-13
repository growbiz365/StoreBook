<x-app-layout>
    @section('title', 'Opening Stock Arms - Arms Management')
    
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'],['url' => '/arms-dashboard', 'label' => 'Arms Dashboard'],['url' => '#', 'label' => 'Opening Stock']]" />

    <!-- Header Section -->
    <div class="bg-gradient-to-r from-orange-50 via-white to-white rounded-xl shadow-sm border border-orange-100 p-6 mb-6 mt-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <div class="p-2 bg-orange-100 rounded-lg">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Opening Stock Arms</h1>
                    <p class="text-sm text-gray-500 mt-1">Arms added through opening stock (not purchases)</p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                @can('create arms')
                <a href="{{ route('arms.create') }}" class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors duration-150 ease-in-out">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add Opening Stock
                </a>
                @endcan
                
                @can('view arms report')
                <a href="{{ route('arms.report') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors duration-150 ease-in-out">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Arms Report
                </a>
                @endcan
            </div>
        </div>
    </div>

    @if (Session::has('success'))
        <x-success-alert message="{{ Session::get('success') }}" />
    @endif

    @if ($errors->has('delete_error'))
        <x-error-alert message="{{ $errors->first('delete_error') }}" />
    @endif

    <!-- Filters Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" action="{{ route('arms.opening-stock') }}" class="space-y-3">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
                <!-- Search -->
                <div class="lg:col-span-2">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500"
                           placeholder="Search serial no...">
                </div>

                <!-- Status Filter -->
                <div>
                    <select name="status" id="status" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>Sold</option>
                        <option value="under_repair" {{ request('status') == 'under_repair' ? 'selected' : '' }}>Under Repair</option>
                        <option value="decommissioned" {{ request('status') == 'decommissioned' ? 'selected' : '' }}>Decommissioned</option>
                    </select>
                </div>

                <!-- Type Filter -->
                <div>
                    <select name="arm_type_id" id="arm_type_id" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                        <option value="">All Types</option>
                        @foreach($armTypes as $type)
                            <option value="{{ $type->id }}" {{ request('arm_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->arm_type }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Category Filter -->
                <div>
                    <select name="arm_category_id" id="arm_category_id" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                        <option value="">All Categories</option>
                        @foreach($armCategories as $category)
                            <option value="{{ $category->id }}" {{ request('arm_category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->arm_category }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Make Filter -->
                <div>
                    <select name="arm_make_id" id="arm_make_id" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                        <option value="">All Makes</option>
                        @foreach($armMakes as $make)
                            <option value="{{ $make->id }}" {{ request('arm_make_id') == $make->id ? 'selected' : '' }}>
                                {{ $make->arm_make }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Caliber Filter -->
                <div>
                    <select name="arm_caliber_id" id="arm_caliber_id" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                        <option value="">All Calibers</option>
                        @foreach($armCalibers as $caliber)
                            <option value="{{ $caliber->id }}" {{ request('arm_caliber_id') == $caliber->id ? 'selected' : '' }}>
                                {{ $caliber->arm_caliber }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Condition Filter (separate row for smaller screens) -->
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-3">
                <div>
                    <select name="arm_condition_id" id="arm_condition_id" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                        <option value="">All Conditions</option>
                        @foreach($armConditions as $condition)
                            <option value="{{ $condition->id }}" {{ request('arm_condition_id') == $condition->id ? 'selected' : '' }}>
                                {{ $condition->arm_condition }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex space-x-2">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors duration-150 ease-in-out">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Filter
                    </button>
                    <a href="{{ route('arms.opening-stock') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-md shadow-sm transition-colors duration-150 ease-in-out">
                        Clear
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Arms List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">
                    Opening Stock Arms ({{ $arms->total() }})
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 ml-2">
                        Opening Stock Only
                    </span>
                </h2>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">Sort by:</span>
                    <select onchange="window.location.href=this.value" class="text-sm border border-gray-300 rounded px-2 py-1">
                        <option value="{{ request()->fullUrlWithQuery(['sort_by' => 'created_at', 'sort_order' => 'desc']) }}" 
                                {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date Added</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort_by' => 'serial_no', 'sort_order' => 'asc']) }}" 
                                {{ request('sort_by') == 'serial_no' ? 'selected' : '' }}>Serial No</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort_by' => 'make', 'sort_order' => 'asc']) }}" 
                                {{ request('sort_by') == 'make' ? 'selected' : '' }}>Make</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort_by' => 'purchase_price', 'sort_order' => 'desc']) }}" 
                                {{ request('sort_by') == 'purchase_price' ? 'selected' : '' }}>Price</option>
                    </select>
                </div>
            </div>
        </div>

        @if($arms->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Arm Details</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type & Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serial No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prices</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Source</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($arms as $arm)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-orange-100 flex items-center justify-center">
                                                <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $arm->arm_title }}</div>
                                            <div class="text-sm text-gray-500">{{ $arm->make_name }} • {{ $arm->armCaliber->arm_caliber }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $arm->armType->arm_type }}</div>
                                    <div class="text-sm text-gray-500">{{ $arm->armCategory->arm_category }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-mono text-gray-900">{{ $arm->serial_no }}</div>
                                    <div class="text-sm text-gray-500">{{ $arm->purchase_date->format('d M Y') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="space-y-1">
                                        <div class="text-sm font-medium text-gray-900">
                                            <span class="text-xs text-gray-500">Initial:</span> PKR {{ number_format($arm->purchase_price, 2) }}
                                        </div>
                                        <div class="text-sm font-medium text-green-600">
                                            <span class="text-xs text-green-500">Sale:</span> PKR {{ number_format($arm->sale_price, 2) }}
                                        </div>
                                        <div class="text-xs text-gray-500">{{ $arm->armCondition->arm_condition }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                        Opening Stock
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $arm->getStatusBadgeColor() }}-100 text-{{ $arm->getStatusBadgeColor() }}-800">
                                        {{ ucfirst(str_replace('_', ' ', $arm->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('arms.show', $arm->id) }}" class="text-orange-600 hover:text-orange-900">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        @if($arm->status === 'available')
                                            <a href="{{ route('arms.edit', $arm->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                        @else
                                            <span class="text-gray-400 cursor-not-allowed" title="Cannot edit {{ ucfirst(str_replace('_', ' ', $arm->status)) }} arm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
                                                </svg>
                                            </span>
                                        @endif
                                        <form action="{{ route('arms.destroy', $arm->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this opening stock arm?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $arms->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No opening stock arms found</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by adding arms to your opening stock.</p>
                <div class="mt-6">
                    @can('create arms')
                    <a href="{{ route('arms.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Add Opening Stock
                    </a>
                    @endcan
                </div>
            </div>
        @endif
    </div>

    <script>
        // Auto-hide success/error messages
        setTimeout(function() {
            const successMessage = document.getElementById('success-message');
            const errorMessage = document.getElementById('error-message');
            
            if (successMessage) {
                successMessage.style.display = 'none';
            }
            if (errorMessage) {
                errorMessage.style.display = 'none';
            }
        }, 5000);
    </script>
</x-app-layout>


