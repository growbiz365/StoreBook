<x-app-layout>
    @section('title', 'Bank Management Dashboard - StoreBook')
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'], ['url' => '#', 'label' => 'Bank Management']]" />

    <!-- Header Section -->
    <div class="bg-gradient-to-r from-indigo-50 via-white to-white rounded-xl shadow-sm border border-indigo-100 p-6 mb-6 mt-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="flex items-center space-x-4 mb-4 md:mb-0">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Bank Management</h1>
                    <p class="text-sm text-gray-500 mt-1">Manage your bank accounts and transactions</p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                @can('create banks')
            <a href="{{ route('banks.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors duration-150 ease-in-out">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add New Account
                </a>
                @endcan
                @can('create bank-transfers')
                <a href="{{ route('bank-transfers.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors duration-150 ease-in-out">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    New Bank Transfer
                </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Bank Accounts -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-50 rounded-lg p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l2-2h14l2 2M5 4v16M19 4v16M3 10h18M3 14h18M3 18h18M3 22h18"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm font-medium text-gray-900">Total Accounts</h2>
                    <p class="text-2xl font-semibold text-gray-700">{{ number_format($totalAccounts) }}</p>
                </div>
            </div>
        </div>

        <!-- Total Bank Balance -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-50 rounded-lg p-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm font-medium text-gray-900">Total Amount</h2>
                    <p class="text-2xl font-semibold {{ $totalBalance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ number_format(round($totalBalance), 0) }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Total Cash Balance -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-amber-50 rounded-lg p-3">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm font-medium text-gray-900">Cash Balance</h2>
                    <p class="text-2xl font-semibold text-gray-700">{{ number_format(round($cashBalance), 0) }}</p>
                </div>
            </div>
        </div>

        <!-- Total Bank Balance -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-50 rounded-lg p-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm font-medium text-gray-900">Bank Balance</h2>
                    <p class="text-2xl font-semibold text-gray-700">{{ number_format(round($bankBalance), 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Transfers + Quick Actions Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Recent Bank Transfers (smaller table) -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between px-4 py-3 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-semibold text-gray-900">Recent Bank Transfers</h2>
                    <p class="text-xs text-gray-500 mt-1">Review the latest inter-account movements and cash flows.</p>
                </div>
                <a href="{{ route('bank-transfers.index') }}" class="mt-2 md:mt-0 inline-flex items-center text-xs font-medium text-indigo-600 hover:text-indigo-900">
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
                                <th class="px-2 py-1 text-left font-medium text-gray-500 uppercase tracking-wide">From</th>
                                <th class="px-2 py-1 text-left font-medium text-gray-500 uppercase tracking-wide">To</th>
                                <th class="px-2 py-1 text-right font-medium text-gray-500 uppercase tracking-wide">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($recentTransfers as $transfer)
                                <tr>
                                    <td class="px-2 py-1 text-gray-500 whitespace-nowrap">{{ $transfer->transfer_date->format('d M Y') }}</td>
                                    <td class="px-2 py-1 text-gray-900">{{ strtoupper($transfer->fromAccount->chartOfAccount->name ?? $transfer->fromAccount->account_name) }}</td>
                                    <td class="px-2 py-1 text-gray-900">{{ strtoupper($transfer->toAccount->chartOfAccount->name ?? $transfer->toAccount->account_name) }}</td>
                                    <td class="px-2 py-1 text-gray-900 text-right">{{ number_format(round($transfer->amount), 0) }}</td>
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
                <p class="text-xs text-gray-500 mt-1">Jump directly to popular bank workflows.</p>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @can('view banks')
                    <a href="{{ route('banks.index') }}" class="flex flex-col justify-between h-28 rounded-2xl border border-indigo-100 bg-indigo-50/80 hover:bg-indigo-50 p-4 transition-all duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex-shrink-0 bg-white rounded-xl p-2 shadow-sm">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l2-2h14l2 2M5 4v16M19 4v16M3 10h18M3 14h18M3 18h18M3 22h18"/>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-2">
                            <h3 class="text-sm font-semibold text-gray-900">Manage Accounts</h3>
                            
                        </div>
                    </a>
                    @endcan

                    @can('view bank-transfers')
                    <a href="{{ route('bank-transfers.index') }}" class="flex flex-col justify-between h-28 rounded-2xl border border-emerald-100 bg-emerald-50/80 hover:bg-emerald-50 p-4 transition-all duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex-shrink-0 bg-white rounded-xl p-2 shadow-sm">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-2">
                            <h3 class="text-sm font-semibold text-gray-900">Bank Transfers</h3>
                            
                        </div>
                    </a>
                    @endcan

                    @can('view bank ledger')
                    <a href="{{ route('banks.ledger-report') }}" class="flex flex-col justify-between h-28 rounded-2xl border border-amber-100 bg-amber-50/80 hover:bg-amber-50 p-4 transition-all duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex-shrink-0 bg-white rounded-xl p-2 shadow-sm">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-2">
                            <h3 class="text-sm font-semibold text-gray-900">Bank Ledger Report</h3>
                            
                        </div>
                    </a>
                    @endcan

                    @can('view bank balances')
                    <a href="{{ route('banks.balances-report') }}" class="flex flex-col justify-between h-28 rounded-2xl border border-purple-100 bg-purple-50/80 hover:bg-purple-50 p-4 transition-all duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex-shrink-0 bg-white rounded-xl p-2 shadow-sm">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-2">
                            <h3 class="text-sm font-semibold text-gray-900">Bank Balances Report</h3>
                            
                        </div>
                    </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>