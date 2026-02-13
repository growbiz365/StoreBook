<x-app-layout>
    @section('title', 'Quotations List - Quotation Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'],
    ['url' => '/sales-dashboard', 'label' => 'Sales Dashboard'],
    ['url' => '#', 'label' => 'Quotations']]" />

    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-50 via-white to-white rounded-xl shadow-sm border border-blue-100 p-6 mb-6 mt-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Quotations</h1>
                    <p class="text-sm text-gray-500 mt-1">Manage and track all quotations</p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
            @can('create quotations')    
            <a href="{{ route('quotations.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors duration-150 ease-in-out">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    New Quotation
                </a>
                @endcan
            </div>
        </div>
    </div>

    @if (Session::has('success'))
        <x-success-alert message="{{ Session::get('success') }}" />
    @endif

    @if (Session::has('error'))
        <x-error-alert message="{{ Session::get('error') }}" />
    @endif

    <!-- Filters Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 px-4 py-3 mb-4">
        <form method="GET" action="{{ route('quotations.index') }}">
            <div class="flex flex-col lg:flex-row lg:items-end lg:space-x-4 space-y-2 lg:space-y-0">
                <!-- Search -->
                <div class="flex-1 min-w-[150px]">
                    <label for="search" class="sr-only">Search</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        class="w-full px-2 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Search...">
                </div>
                <!-- Status -->
                <div class="min-w-[120px]">
                    <label for="status" class="sr-only">Status</label>
                    <select name="status" id="status"
                        class="w-full px-2 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Statuses</option>
                        <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                        
                        <option value="converted" {{ request('status') == 'converted' ? 'selected' : '' }}>Converted</option>
                    </select>
                </div>
                <!-- Party -->
                <div class="min-w-[120px]">
                    <label for="customer" class="sr-only">Party</label>
                    <select name="customer" id="customer"
                        class="w-full px-2 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Parties</option>
                        @foreach($customers ?? [] as $customer)
                            <option value="{{ $customer->id }}" {{ request('customer') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <!-- Payment Type -->
                <div class="min-w-[120px]">
                    <label for="payment_type" class="sr-only">Payment Type</label>
                    <select name="payment_type" id="payment_type"
                        class="w-full px-2 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Types</option>
                        <option value="cash" {{ request('payment_type') == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="credit" {{ request('payment_type') == 'credit' ? 'selected' : '' }}>Credit</option>
                    </select>
                </div>
                <!-- Date From -->
                <div class="min-w-[120px]">
                    <label for="date_from" class="sr-only">Date From</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                        class="w-full px-2 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <!-- Date To -->
                <div class="min-w-[120px]">
                    <label for="date_to" class="sr-only">Date To</label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                        class="w-full px-2 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <!-- Actions -->
                <div class="flex items-center space-x-2 mt-2 lg:mt-0">
                    <button type="submit"
                        class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-md shadow-sm transition-colors duration-150 ease-in-out">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Filter
                    </button>
                    <a href="{{ route('quotations.index') }}"
                        class="text-xs text-gray-500 hover:text-gray-700 px-2 py-1">Clear</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Quotations List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 via-white to-white">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Quotation Records</h2>
                        <p class="text-sm text-gray-500">Total Records: {{ $quotations->total() }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if($quotations->isEmpty())
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No quotations found</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new quotation.</p>
                @can('create quotations')
                <div class="mt-6">
                    <a href="{{ route('quotations.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        New Quotation
                    </a>
                </div>
                @endcan
            </div>
        @else
            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quotation</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Type</th>
                           
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($quotations as $quotation)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div class="h-10 w-10 flex items-center justify-center bg-blue-100 rounded-lg">
                                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="flex items-center space-x-2">
                                                <div class="text-sm font-medium text-gray-900">{{ $quotation->quotation_number }}</div>
                                                @if($quotation->isExpiringSoon() && $quotation->isSent())
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800" title="Expiring Soon">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        {{ $quotation->getDaysUntilExpiry() }} days left
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $quotation->generalLines->count() }} items • {{ $quotation->armLines->count() }} arms
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $quotation->party->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $quotation->party->cnic ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                            {{ ucfirst($quotation->payment_type) }}
                                        </span>
                                        @if($quotation->bank)
                                            <div class="mt-1">{{ $quotation->bank->account_name }}</div>
                                        @endif
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">@currency($quotation->total_amount)</div>
                                    <div class="text-sm text-gray-500">Subtotal: @currency($quotation->subtotal)</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        @if($quotation->status == 'sent') bg-blue-100 text-blue-800
                                        @elseif($quotation->status == 'expired') bg-orange-100 text-orange-800
                                        @elseif($quotation->status == 'rejected') bg-red-100 text-red-800
                                        @else bg-green-100 text-green-800 @endif">
                                        {{ ucfirst($quotation->status) }}
                                    </span>
                                    @if($quotation->isConverted())
                                        <div class="text-xs text-gray-500 mt-1">
                                            <a href="{{ route('sale-invoices.show', $quotation->converted_to_sale_id) }}" class="hover:underline">
                                                Sale #{{ $quotation->converted_to_sale_id }}
                                            </a>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        @can('view quotations')
                                        <a href="{{ route('quotations.show', $quotation) }}" class="text-blue-600 hover:text-blue-900" title="View">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        @endcan
                                        @if($quotation->canBeEdited())
                                        @can('edit quotations')
                                            <a href="{{ route('quotations.edit', $quotation) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                        @endcan
                                        @endif
                                        @if($quotation->canBeDeleted())
                                        @can('delete quotations')
                                            <form action="{{ route('quotations.destroy', $quotation) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this quotation?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endcan
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $quotations->links() }}
            </div>
        @endif
    </div>
</x-app-layout>

