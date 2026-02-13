<x-app-layout>
    @section('title', 'Party Management Dashboard - StoreBook')
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'], ['url' => '#', 'label' => 'Party Management']]" />

    <!-- Header Section -->
    <div class="bg-gradient-to-r from-indigo-50 via-white to-white rounded-xl shadow-sm border border-indigo-100 p-6 mb-6 mt-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="flex items-center space-x-4 mb-4 md:mb-0">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Party Management</h1>
                    <p class="text-sm text-gray-500 mt-1">Manage your business parties and transactions</p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                @can('create parties')
            <a href="{{ route('parties.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors duration-150 ease-in-out">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add New Party
                </a>
                @endcan
                @can('create parties transfers')
                <a href="{{ route('party-transfers.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors duration-150 ease-in-out">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    New Party Transfer
                </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Parties -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-50 rounded-lg p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm font-medium text-gray-900">Total Parties</h2>
                    <p class="text-2xl font-semibold text-gray-700">{{ number_format($totalParties) }}</p>
                </div>
            </div>
        </div>

        
        

        

        
        <!-- Account Payable -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-50 rounded-lg p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm font-medium text-gray-900">Account Payable</h2>
                    <p class="text-2xl font-semibold text-gray-700">{{ number_format($accountPayable, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Account Receivable -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-50 rounded-lg p-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm font-medium text-gray-900">Account Receivable</h2>
                    <p class="text-2xl font-semibold text-gray-700">{{ number_format($accountReceivable, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Total Balance -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-50 rounded-lg p-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v2m0 14v2m7-7h2M3 12H5m12.364-7.364l1.414 1.414M5.222 18.778l1.414-1.414m12.728 0l-1.414-1.414M5.222 5.222l1.414 1.414" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm font-medium text-gray-900">Net Balance</h2>
                    <p class="text-2xl font-semibold {{ $totalBalance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ number_format($totalBalance, 2) }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Transfers + Quick Links Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Recent Party Transfers (smaller table) -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between px-4 py-3 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-semibold text-gray-900">Recent Party Transfers</h2>
                    <p class="text-xs text-gray-500 mt-1">Latest balance movements between parties.</p>
                </div>
                <a href="{{ route('party-transfers.index') }}" class="mt-2 md:mt-0 inline-flex items-center text-xs font-medium text-indigo-600 hover:text-indigo-900">
                    View All
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
            <div class="p-4">
                <div class="max-h-64 overflow-y-auto overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-2 py-1 text-left font-medium text-gray-500 uppercase tracking-wide">Date</th>
                                <th class="px-2 py-1 text-left font-medium text-gray-500 uppercase tracking-wide">Debit Party</th>
                                <th class="px-2 py-1 text-left font-medium text-gray-500 uppercase tracking-wide">Credit Party</th>
                                <th class="px-2 py-1 text-right font-medium text-gray-500 uppercase tracking-wide">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($recentTransfers as $transfer)
                                <tr class="{{ $transfer->debitParty->status == 0 || $transfer->creditParty->status == 0 ? 'opacity-60' : '' }}">
                                    <td class="px-2 py-1 text-gray-500 whitespace-nowrap">{{ $transfer->date->format('d M Y') }}</td>
                                    <td class="px-2 py-1 text-gray-900">
                                        {{ $transfer->debitParty->name }}
                                        @if($transfer->debitParty->status == 0)
                                            <span class="ml-1 px-1 py-0.5 inline-flex text-[10px] leading-4 font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-2 py-1 text-gray-900">
                                        {{ $transfer->creditParty->name }}
                                        @if($transfer->creditParty->status == 0)
                                            <span class="ml-1 px-1 py-0.5 inline-flex text-[10px] leading-4 font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-2 py-1 text-gray-900 text-right">{{ number_format($transfer->transfer_amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-2 py-3 text-gray-500 text-center">No recent transfers</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-4 py-3 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-900">Quick Actions</h2>
                <p class="text-xs text-gray-500 mt-1">Jump directly to popular party workflows.</p>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @can('view parties')
                    <a href="{{ route('parties.index') }}" class="flex flex-col justify-between h-28 rounded-2xl border border-indigo-100 bg-indigo-50/80 hover:bg-indigo-50 p-4 transition-all duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex-shrink-0 bg-white rounded-xl p-2 shadow-sm">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-2">
                            <h3 class="text-sm font-semibold text-gray-900">Manage Parties</h3>
                            
                        </div>
                    </a>
                    @endcan

                    @can('view parties transfers')
                    <a href="{{ route('party-transfers.index') }}" class="flex flex-col justify-between h-28 rounded-2xl border border-emerald-100 bg-emerald-50/80 hover:bg-emerald-50 p-4 transition-all duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex-shrink-0 bg-white rounded-xl p-2 shadow-sm">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-2">
                            <h3 class="text-sm font-semibold text-gray-900">Party Transfers</h3>
                            
                        </div>
                    </a>
                    @endcan

                    @can('view parties ledger')
                    <a href="{{ route('parties.ledger-report') }}" class="flex flex-col justify-between h-28 rounded-2xl border border-amber-100 bg-amber-50/80 hover:bg-amber-50 p-4 transition-all duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex-shrink-0 bg-white rounded-xl p-2 shadow-sm">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-2">
                            <h3 class="text-sm font-semibold text-gray-900">Party Ledger Report</h3>
                           
                        </div>
                    </a>
                    @endcan

                    @can('view parties balances')
                    <a href="{{ route('parties.balances-report') }}" class="flex flex-col justify-between h-28 rounded-2xl border border-purple-100 bg-purple-50/80 hover:bg-purple-50 p-4 transition-all duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex-shrink-0 bg-white rounded-xl p-2 shadow-sm">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-2">
                            <h3 class="text-sm font-semibold text-gray-900">Party Balances Report</h3>
                            
                        </div>
                    </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 