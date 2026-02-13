<x-app-layout>
    @section('title', 'Sales List - Sales Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'],['url' => '/sales-dashboard', 'label' => 'Sales Dashboard'],['url' => '#', 'label' => 'Sales']]" />

    <!-- Header Section -->
    <div class="bg-gradient-to-r from-green-50 via-white to-white rounded-xl shadow-sm border border-green-100 p-6 mb-6 mt-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Sale Invoices</h1>
                    <p class="text-sm text-gray-500 mt-1">Manage and track all sale transactions</p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
            @can('create sales')    
            <a href="{{ route('sale-invoices.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors duration-150 ease-in-out">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    New Sale Invoice
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

    <!-- Filters Section (Compact) -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 px-4 py-3 mb-4">
        <form method="GET" action="{{ route('sale-invoices.index') }}">
            <div class="flex flex-col lg:flex-row lg:items-end lg:space-x-4 space-y-2 lg:space-y-0">
                <!-- Search -->
                <div class="flex-1 min-w-[150px]">
                    <label for="search" class="sr-only">Search</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        class="w-full px-2 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-green-500 focus:border-green-500"
                        placeholder="Search...">
                </div>
                <!-- Status -->
                <div class="min-w-[120px]">
                    <label for="status" class="sr-only">Status</label>
                    <select name="status" id="status"
                        class="w-full px-2 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                        <option value="">All Statuses</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="posted" {{ request('status') == 'posted' ? 'selected' : '' }}>Posted</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <!-- Party -->
                <div class="min-w-[120px]">
                    <label for="customer" class="sr-only">Party</label>
                    <select name="customer" id="customer"
                        class="w-full px-2 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                        <option value="">All Parties</option>
                        @foreach($customers ?? [] as $customer)
                            <option value="{{ $customer->id }}" {{ request('customer') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <!-- Sale Type -->
                <div class="min-w-[120px]">
                    <label for="sale_type" class="sr-only">Sale Type</label>
                    <select name="sale_type" id="sale_type"
                        class="w-full px-2 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                        <option value="">All Types</option>
                        <option value="cash" {{ request('sale_type') == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="credit" {{ request('sale_type') == 'credit' ? 'selected' : '' }}>Credit</option>
                    </select>
                </div>
                <!-- Date From -->
                <div class="min-w-[120px]">
                    <label for="date_from" class="sr-only">Date From</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                        class="w-full px-2 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-green-500 focus:border-green-500"
                        placeholder="From">
                </div>
                <!-- Date To -->
                <div class="min-w-[120px]">
                    <label for="date_to" class="sr-only">Date To</label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                        class="w-full px-2 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-green-500 focus:border-green-500"
                        placeholder="To">
                </div>
                <!-- Actions -->
                <div class="flex items-center space-x-2 mt-2 lg:mt-0">
                    <button type="submit"
                        class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-md shadow-sm transition-colors duration-150 ease-in-out">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Filter
                    </button>
                    <a href="{{ route('sale-invoices.index') }}"
                        class="text-xs text-gray-500 hover:text-gray-700 px-2 py-1">Clear</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Sale Invoices List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-50 via-white to-white">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Sale Transactions</h2>
                        <p class="text-sm text-gray-500">Total Records: {{ $saleInvoices->total() }}</p>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <span class="text-sm font-medium text-gray-600">Sort by:</span>
                    <select onchange="window.location.href=this.value" 
                            class="text-sm border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200 appearance-none">
                        <option value="{{ request()->fullUrlWithQuery(['sort_by' => 'created_at', 'sort_order' => 'desc']) }}"
                                {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>
                            <span class="flex items-center">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Date Created
                            </span>
                        </option>
                        <option value="{{ request()->fullUrlWithQuery(['sort_by' => 'id', 'sort_order' => 'desc']) }}"
                                {{ request('sort_by') == 'id' ? 'selected' : '' }}>
                            <span class="flex items-center">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                </svg>
                                Invoice ID
                            </span>
                        </option>
                        <option value="{{ request()->fullUrlWithQuery(['sort_by' => 'invoice_date', 'sort_order' => 'desc']) }}"
                                {{ request('sort_by') == 'invoice_date' ? 'selected' : '' }}>
                            <span class="flex items-center">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Invoice Date
                            </span>
                        </option>
                        <option value="{{ request()->fullUrlWithQuery(['sort_by' => 'total_amount', 'sort_order' => 'desc']) }}"
                                {{ request('sort_by') == 'total_amount' ? 'selected' : '' }}>
                            <span class="flex items-center">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                </svg>
                                Total Amount
                            </span>
                        </option>
                    </select>
                </div>
            </div>
        </div>

        @if($saleInvoices->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sale Details</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Party & Payment</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($saleInvoices as $saleInvoice)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="flex items-center space-x-2">
                                                <div class="text-sm font-medium text-gray-900">Sale #{{ $saleInvoice->id }}</div>
                                                @if($saleInvoice->approval_id)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800" title="Generated from Approval">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                        </svg>
                                                        <a href="{{ route('approvals.show', $saleInvoice->approval_id) }}" class="hover:underline">Approval #{{ $saleInvoice->approval_id }}</a>
                                                    </span>
                                                @endif
                                                @if($saleInvoice->quotation_id)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800" title="Generated from Quotation">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                        </svg>
                                                        <a href="{{ route('quotations.show', $saleInvoice->quotation_id) }}" class="hover:underline">{{ $saleInvoice->quotation->quotation_number ?? 'Quote #' . $saleInvoice->quotation_id }}</a>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $saleInvoice->generalLines->count() }} general items • {{ $saleInvoice->armLines->count() }} arm lines
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $saleInvoice->party->name ?? '-' }}</div>
                                    <div class="text-sm text-gray-500">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                            {{ ucfirst($saleInvoice->sale_type) }}
                                        </span>
                                        @if($saleInvoice->bank)
                                            • {{ $saleInvoice->bank->chartOfAccount->name ?? $saleInvoice->bank->account_name }}
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">@businessDate($saleInvoice->invoice_date)</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">@currency($saleInvoice->total_amount)</div>
                                    <div class="text-sm text-gray-500">
                                        Subtotal: @currency($saleInvoice->subtotal)
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        @if($saleInvoice->status == 'draft') bg-gray-100 text-gray-800
                                        @elseif($saleInvoice->status == 'posted') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($saleInvoice->status) }}
                                    </span>
                                    @if($saleInvoice->status == 'posted')
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $saleInvoice->generalLines->count() }} items • {{ $saleInvoice->armLines->count() }} arms
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        @can('view sales')
                                        <a href="{{ route('sale-invoices.show', $saleInvoice) }}" 
                                           class="text-blue-600 hover:text-blue-900" title="View Details">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        @endcan
                                        @can('edit sales')
                                        @if(!$saleInvoice->approval_id && $saleInvoice->canBeEdited())
                                            <a href="{{ route('sale-invoices.edit', $saleInvoice) }}" 
                                               class="text-green-600 hover:text-green-900" title="Edit Sale Invoice">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                        @endif
                                        @endcan
                                        @can('cancel sales')
                                        @if(!$saleInvoice->approval_id && $saleInvoice->canBeCancelled())
                                            <form action="{{ route('sale-invoices.cancel', $saleInvoice) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-red-600 hover:text-red-900" 
                                                        onclick="return confirm('Are you sure you want to cancel this sale invoice? This will restore inventory and cannot be undone.')" 
                                                        title="Cancel Sale Invoice">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                           @endcan 
                                        @if($saleInvoice->canBePosted())
                                            <form action="{{ route('sale-invoices.post', $saleInvoice) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-900" 
                                                        onclick="return confirm('Are you sure you want to post this sale invoice? This will create inventory entries.')" 
                                                        title="Post Sale Invoice">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </button>
                                            </form>
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
                {{ $saleInvoices->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No sale invoices found</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new sale invoice transaction.</p>
                <div class="mt-6">
                    <a href="{{ route('sale-invoices.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        New Sale Invoice
                    </a>
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
