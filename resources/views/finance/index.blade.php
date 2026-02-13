<x-app-layout>
    @section('title', 'Finance Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        ['url' => '/finance', 'label' => 'Finance'],
    ]" />

    <!-- Header Section -->
    <div class="bg-gradient-to-r from-green-50 via-white to-white rounded-xl shadow-sm border border-green-100 p-6 mb-6">
        <div class="flex items-center justify-between">
            <!-- Left: Icon, Title, Subtitle -->
            <div class="flex items-center space-x-4 mb-4 md:mb-0">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Finance Management</h1>
                    <p class="text-sm text-gray-500 mt-1">Track and manage your business's financial activities</p>
                </div>
            </div>
            <!-- Right: Fiscal Year, Dropdown -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                <span class="bg-green-50 px-3 py-1 rounded-full border border-green-100 text-sm text-gray-500">Fiscal Year: {{ now()->year }}</span>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        
        @can('module', 'view account types')
        <a href="{{ route('account-types.index') }}" class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow duration-200 flex items-center space-x-3">
            <div class="flex-shrink-0 bg-indigo-50 p-2 rounded-lg">
                <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-900">Account Types</h3>
                <p class="text-xs text-gray-500">Manage account types</p>
            </div>
        </a>
        @endcan

        @can('module', 'view chart of accounts')
        <a href="{{ route('chart-of-accounts.index') }}" class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow duration-200 flex items-center space-x-3">
            <div class="flex-shrink-0 bg-green-50 p-2 rounded-lg">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-900">Chart of Accounts</h3>
                <p class="text-xs text-gray-500">View all accounts</p>
            </div>
        </a>
        @endcan

        @can('module', 'view journal entries')
        <a href="{{ route('journal-entries.index') }}" class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow duration-200 flex items-center space-x-3">
            <div class="flex-shrink-0 bg-red-50 p-2 rounded-lg">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-900">Journal Entries</h3>
                <p class="text-xs text-gray-500">View all entries</p>
            </div>
        </a>
        @endcan

        @can('view general vouchers')
        <a href="{{ url('general-vouchers') }}" class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow duration-200 flex items-center space-x-3">
            <div class="flex-shrink-0 bg-green-50 p-2 rounded-lg">
            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-900">General Vouchers</h3>
                <p class="text-xs text-gray-500">View all general vouchers</p>
            </div>
        </a>
        @endcan

        @can('view trial-balance')
        <a href="{{ route('trial-balance.index') }}" class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow duration-200 flex items-center space-x-3">
            <div class="flex-shrink-0 bg-purple-50 p-2 rounded-lg">
                <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-900">Trial Balance</h3>
                <p class="text-xs text-gray-500">View trial balance report</p>
            </div>
        </a>
        @endcan
        
        @can('module', 'view account reports')
        <a href="{{ route('reports.accounting.index') }}" class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow duration-200 flex items-center space-x-3">
            <div class="flex-shrink-0 bg-purple-50 p-2 rounded-lg">
                <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-900">Financial Reports</h3>
                <p class="text-xs text-gray-500">View financial reports</p>
            </div>
        </a>
        @endcan

        @can('view balance sheet')
        <a href="{{ route('balance-sheet.index') }}" class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow duration-200 flex items-center space-x-3">
            <div class="flex-shrink-0 bg-blue-50 p-2 rounded-lg">
                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-900">Balance Sheet</h3>
                <p class="text-xs text-gray-500">View balance sheet report</p>
            </div>
        </a>
        @endcan

        @can('view profit-loss-report')
        <a href="{{ route('profit-loss.index') }}" class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow duration-200 flex items-center space-x-3">
            <div class="flex-shrink-0 bg-green-50 p-2 rounded-lg">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-900">Profit & Loss</h3>
                <p class="text-xs text-gray-500">View P&L statement</p>
            </div>
        </a>
        @endcan

        @can('view general ledger')
        <a href="{{ route('general-ledger.index') }}" class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow duration-200 flex items-center space-x-3">
            <div class="flex-shrink-0 bg-purple-50 p-2 rounded-lg">
                <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-900">General Ledger</h3>
                <p class="text-xs text-gray-500">View general ledger report</p>
            </div>
        </a>
        @endcan

        @can('view detailed-general-ledger')
        <a href="{{ route('finance.detailed-general-ledger.index') }}" class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow duration-200 flex items-center space-x-3">
            <div class="flex-shrink-0 bg-orange-50 p-2 rounded-lg">
                <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-900">Detailed General Ledger</h3>
                <p class="text-xs text-gray-500">View detailed ledger with running balance</p>
            </div>
        </a>
        @endcan
        
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Income Card -->
        <div class="bg-gradient-to-br from-green-50 to-white rounded-xl shadow-sm border border-green-100 overflow-hidden hover:shadow-md transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Total Income</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $currency->symbol }} {{ number_format($statistics['total_income'], 2) }}</h3>
                    </div>
                    <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statistics['income_change'] >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        <svg class="-ml-0.5 mr-1.5 h-2 w-2 {{ $statistics['income_change'] >= 0 ? 'text-green-400' : 'text-red-400' }}" fill="currentColor" viewBox="0 0 8 8">
                            <circle cx="4" cy="4" r="3" />
                        </svg>
                        {{ number_format($statistics['income_change'], 1) }}% from last month
                    </span>
                </div>
            </div>
            <div class="bg-green-50/50 px-6 py-3 border-t border-green-100">
                <div class="text-sm text-gray-600">
                    <span class="font-medium text-gray-900">{{ number_format($statistics['average_monthly_income'], 2) }} {{ $currency->symbol }}</span> average per month
                </div>
            </div>
        </div>

        <!-- Total Expenses Card -->
        <div class="bg-gradient-to-br from-red-50 to-white rounded-xl shadow-sm border border-red-100 overflow-hidden hover:shadow-md transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Total Expenses</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ number_format($statistics['total_expenses'], 2) }} {{ $currency->symbol }}</h3>
                    </div>
                    <div class="h-12 w-12 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statistics['expenses_change'] >= 0 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                        <svg class="-ml-0.5 mr-1.5 h-2 w-2 {{ $statistics['expenses_change'] >= 0 ? 'text-red-400' : 'text-green-400' }}" fill="currentColor" viewBox="0 0 8 8">
                            <circle cx="4" cy="4" r="3" />
                        </svg>
                        {{ number_format($statistics['expenses_change'], 1) }}% from last month
                    </span>
                </div>
            </div>
            <div class="bg-red-50/50 px-6 py-3 border-t border-red-100">
                <div class="text-sm text-gray-600">
                    <span class="font-medium text-gray-900">{{ number_format($statistics['average_monthly_expenses'], 2) }} {{ $currency->symbol }}</span> average per month
                </div>
            </div>
        </div>

        <!-- Net Balance Card -->
        <div class="bg-gradient-to-br from-blue-50 to-white rounded-xl shadow-sm border border-blue-100 overflow-hidden hover:shadow-md transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Net Balance</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ number_format($statistics['net_balance'], 2) }} {{ $currency->symbol }}</h3>
                    </div>
                    <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statistics['net_balance'] >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        <svg class="-ml-0.5 mr-1.5 h-2 w-2 {{ $statistics['net_balance'] >= 0 ? 'text-green-400' : 'text-red-400' }}" fill="currentColor" viewBox="0 0 8 8">
                            <circle cx="4" cy="4" r="3" />
                        </svg>
                        Current balance
                    </span>
                </div>
            </div>
            <div class="bg-blue-50/50 px-6 py-3 border-t border-blue-100">
                <div class="text-sm text-gray-600">
                    <span class="font-medium text-gray-900">{{ number_format($statistics['average_monthly_income'] - $statistics['average_monthly_expenses'], 2) }} {{ $currency->symbol }}</span> average monthly profit
                </div>
            </div>
        </div>

        {{-- <!-- Pending Payments Card -->
        <div class="bg-gradient-to-br from-purple-50 to-white rounded-xl shadow-sm border border-purple-100 overflow-hidden hover:shadow-md transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Pending Payments</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ number_format($statistics['pending_payments'], 2) }} {{ $currency->symbol }}</h3>
                    </div>
                    <div class="h-12 w-12 rounded-full bg-purple-100 flex items-center justify-center">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                        <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-purple-400" fill="currentColor" viewBox="0 0 8 8">
                            <circle cx="4" cy="4" r="3" />
                        </svg>
                        {{ $statistics['pending_transactions_count'] }} transactions
                    </span>
                </div>
            </div>
            <div class="bg-purple-50/50 px-6 py-3 border-t border-purple-100">
                <div class="text-sm text-gray-600">
                    <span class="font-medium text-gray-900">{{ number_format($statistics['average_transaction_amount'], 2) }} {{ $currency->symbol }}</span> average per transaction
                </div>
            </div>
        </div> --}}
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Income vs Expenses Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Income vs Expenses</h3>
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <span class="w-2 h-2 bg-green-400 rounded-full mr-1"></span>
                        Income
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        <span class="w-2 h-2 bg-red-400 rounded-full mr-1"></span>
                        Expenses
                    </span>
                </div>
            </div>
            <div class="h-80">
                <canvas id="incomeExpensesChart"></canvas>
            </div>
        </div>

        <!-- Monthly Trends Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Monthly Trends</h3>
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <span class="w-2 h-2 bg-green-400 rounded-full mr-1"></span>
                        Income
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        <span class="w-2 h-2 bg-red-400 rounded-full mr-1"></span>
                        Expenses
                    </span>
                </div>
            </div>
            <div class="h-80">
                <canvas id="monthlyTrendsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Links Grid -->
   

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Common chart options
        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f3f4f6',
                        drawBorder: false
                    },
                    ticks: {
                        callback: function(value) {
                            return '{{ $currency->symbol }} ' + value.toLocaleString();
                        },
                        font: {
                            size: 11
                        },
                        padding: 10
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        },
                        padding: 10
                    }
                }
            }
        };

        // Income vs Expenses Chart
        const incomeExpensesCtx = document.getElementById('incomeExpensesChart').getContext('2d');
        new Chart(incomeExpensesCtx, {
            type: 'bar',
            data: {
                labels: ['Income', 'Expenses'],
                datasets: [{
                    data: [
                        {{ $statistics['total_income'] }},
                        {{ $statistics['total_expenses'] }}
                    ],
                    backgroundColor: [
                        'rgba(34, 197, 94, 0.2)',
                        'rgba(239, 68, 68, 0.2)'
                    ],
                    borderColor: [
                        'rgb(34, 197, 94)',
                        'rgb(239, 68, 68)'
                    ],
                    borderWidth: 2,
                    borderRadius: 6,
                    barThickness: 40
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '{{ $currency->symbol }} ' + context.raw.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // Monthly Trends Chart
        const monthlyTrendsCtx = document.getElementById('monthlyTrendsChart').getContext('2d');
        new Chart(monthlyTrendsCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Income',
                    data: [{{ implode(',', array_fill(0, 12, 0)) }}], // Replace with actual monthly data
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 2,
                    pointRadius: 4,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: 'rgb(34, 197, 94)',
                    pointBorderWidth: 2
                }, {
                    label: 'Expenses',
                    data: [{{ implode(',', array_fill(0, 12, 0)) }}], // Replace with actual monthly data
                    borderColor: 'rgb(239, 68, 68)',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 2,
                    pointRadius: 4,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: 'rgb(239, 68, 68)',
                    pointBorderWidth: 2
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': {{ $currency->symbol }} ' + context.raw.toLocaleString();
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    </script>
</x-app-layout>