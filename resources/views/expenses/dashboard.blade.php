<x-app-layout>
    @section('title', 'Expense Dashboard - StoreBook')
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'],  ['url' => '#', 'label' => 'Expense Dashboard']]" />

    <!-- Header with inline filters -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4">
        <div>
            <x-dynamic-heading title="Expense Dashboard" />
            <p class="text-xs text-slate-500 mt-1">Track and analyze your business expenses</p>
        </div>
        
        <!-- Enhanced Filters -->
        <form method="GET" class="flex flex-nowrap items-end gap-2 bg-white rounded-xl shadow-sm border border-slate-200 p-3">
            <div class="flex flex-col flex-shrink-0">
                <label for="filter_type" class="text-xs font-semibold text-slate-700 mb-1">Period</label>
                <select id="filter_type" name="filter_type" class="w-28 rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-xs h-8 px-2 bg-white transition-all">
                    <option value="current_month" {{ !request('filter_type') || request('filter_type') == 'current_month' ? 'selected' : '' }}>This Month</option>
                    <option value="custom" {{ request('filter_type') == 'custom' ? 'selected' : '' }}>Custom</option>
                    <option value="yearly" {{ request('filter_type') == 'yearly' ? 'selected' : '' }}>This Year</option>
                </select>
            </div>
            <div class="flex flex-col flex-shrink-0">
                <label for="from_date" class="text-xs font-semibold text-slate-700 mb-1">From</label>
                <input type="date" name="from_date" id="from_date" value="{{ $filterData['from_date'] }}" class="w-28 rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-xs h-8 px-2 bg-white transition-all">
            </div>
            <div class="flex flex-col flex-shrink-0">
                <label for="to_date" class="text-xs font-semibold text-slate-700 mb-1">To</label>
                <input type="date" name="to_date" id="to_date" value="{{ $filterData['to_date'] }}" class="w-28 rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-xs h-8 px-2 bg-white transition-all">
            </div>
            <div class="flex gap-1.5 flex-shrink-0">
                <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-semibold rounded-lg shadow-sm text-white bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-indigo-500 h-8 transition-all">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Filter
                </button>
                @if(request()->has('from_date') || request()->has('to_date') || request()->has('filter_type'))
                    <a href="{{ url()->current() }}" class="inline-flex items-center px-3 py-1.5 border border-slate-300 text-xs font-semibold rounded-lg text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-slate-500 h-8 transition-all whitespace-nowrap">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Period Info Badge -->
    <div class="mb-4">
        <div class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg border border-indigo-100">
            <svg class="w-4 h-4 mr-1.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2z" />
            </svg>
            <span class="text-xs text-slate-700 font-medium">
                Showing data for:
                <span class="font-bold text-indigo-700 ml-1">
                    @php($period = request('filter_type'))
                    @if($period === 'current_month' || !$period)
                        This Month
                    @elseif($period === 'yearly')
                        This Year
                    @else
                        {{ \Carbon\Carbon::parse($filterData['from_date'])->format('d/m/Y') }}
                        @if($filterData['to_date'] && $filterData['from_date'] != $filterData['to_date'])
                            to {{ \Carbon\Carbon::parse($filterData['to_date'])->format('d/m/Y') }}
                        @endif
                    @endif
                </span>
            </span>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
        <!-- Total Expenses -->
        <div class="group relative bg-gradient-to-br from-white to-slate-50 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-slate-200 overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-red-100 to-orange-100 rounded-full -mr-16 -mt-16 opacity-20 group-hover:opacity-30 transition-opacity"></div>
            <div class="relative p-4">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-semibold text-slate-600 mb-1 uppercase tracking-wide">Total Expenses</p>
                        <p class="text-xl font-bold text-slate-900 mb-0.5">{{ number_format(round($totalExpenses), 0) }}</p>
                        <p class="text-xs text-slate-500 font-medium">
                            @if($filterData['filter_type'] == 'current_month')
                                Current Month
                            @elseif($filterData['filter_type'] == 'custom')
                                Custom Range
                            @elseif($filterData['filter_type'] == 'yearly')
                                Year {{ now()->year }}
                            @else
                                Current Month
                            @endif
                        </p>
                    </div>
                    <div class="flex-shrink-0 bg-gradient-to-br from-red-500 to-orange-500 rounded-2xl p-3 shadow-lg transform group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Average -->
        <div class="group relative bg-gradient-to-br from-white to-slate-50 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-slate-200 overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-full -mr-16 -mt-16 opacity-20 group-hover:opacity-30 transition-opacity"></div>
            <div class="relative p-4">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-semibold text-slate-600 mb-1 uppercase tracking-wide">Monthly Average</p>
                        <p class="text-xl font-bold text-slate-900 mb-0.5">{{ number_format(round($monthlyAverage), 0) }}</p>
                        <p class="text-xs text-slate-500 font-medium">Per month</p>
                    </div>
                    <div class="flex-shrink-0 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-2xl p-3 shadow-lg transform group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Entries -->
        <div class="group relative bg-gradient-to-br from-white to-slate-50 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-slate-200 overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full -mr-16 -mt-16 opacity-20 group-hover:opacity-30 transition-opacity"></div>
            <div class="relative p-4">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-semibold text-slate-600 mb-1 uppercase tracking-wide">Total Entries</p>
                        <p class="text-xl font-bold text-slate-900 mb-0.5">{{ $totalEntries }}</p>
                        <p class="text-xs text-slate-500 font-medium">
                            @if($filterData['filter_type'] == 'current_month')
                                Current Month
                            @elseif($filterData['filter_type'] == 'custom')
                                Custom Range
                            @elseif($filterData['filter_type'] == 'yearly')
                                Year {{ now()->year }}
                            @else
                                Current Month
                            @endif
                        </p>
                    </div>
                    <div class="flex-shrink-0 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-2xl p-3 shadow-lg transform group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Expense Head -->
        <div class="group relative bg-gradient-to-br from-white to-slate-50 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-slate-200 overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-amber-100 to-yellow-100 rounded-full -mr-16 -mt-16 opacity-20 group-hover:opacity-30 transition-opacity"></div>
            <div class="relative p-4">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-semibold text-slate-600 mb-1 uppercase tracking-wide">Top Expense Head</p>
                        <p class="text-base font-bold text-slate-900 mb-0.5 truncate">{{ $topExpenseHead }}</p>
                        <p class="text-xs text-slate-500 font-medium">Highest spending</p>
                    </div>
                    <div class="flex-shrink-0 bg-gradient-to-br from-amber-500 to-yellow-500 rounded-2xl p-3 shadow-lg transform group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
        @can('view expenses')
        <a href="{{ route('expenses.index') }}" class="group relative bg-gradient-to-br from-white to-slate-50 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-200 p-4 overflow-hidden transform hover:-translate-y-0.5">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-indigo-500/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="relative flex items-center">
                <div class="flex-shrink-0 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl p-2.5 shadow-md group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-xs font-bold text-slate-900 group-hover:text-blue-600 transition-colors">Manage Expenses</h3>
                    <p class="text-xs text-slate-500 mt-0.5 font-medium">View and manage all expenses</p>
                </div>
            </div>
        </a>
        @endcan
        @can('create expenses')
        <a href="{{ route('expenses.create') }}" class="group relative bg-gradient-to-br from-white to-slate-50 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-200 p-4 overflow-hidden transform hover:-translate-y-0.5">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-teal-500/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="relative flex items-center">
                <div class="flex-shrink-0 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl p-2.5 shadow-md group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-xs font-bold text-slate-900 group-hover:text-emerald-600 transition-colors">Add New Expense</h3>
                    <p class="text-xs text-slate-500 mt-0.5 font-medium">Create a new expense entry</p>
                </div>
            </div>
        </a>
        @endcan
        @can('view expense heads')
        <a href="{{ route('expense-heads.index') }}" class="group relative bg-gradient-to-br from-white to-slate-50 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-200 p-4 overflow-hidden transform hover:-translate-y-0.5">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-500/5 to-pink-500/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="relative flex items-center">
                <div class="flex-shrink-0 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl p-2.5 shadow-md group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-xs font-bold text-slate-900 group-hover:text-purple-600 transition-colors">Expense Heads</h3>
                    <p class="text-xs text-slate-500 mt-0.5 font-medium">Manage expense categories</p>
                </div>
            </div>
        </a>
        @endcan
        @can('view expense heads')
        <a href="{{ route('expenses.report') }}" class="group relative bg-gradient-to-br from-white to-slate-50 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-200 p-4 overflow-hidden transform hover:-translate-y-0.5">
            <div class="absolute inset-0 bg-gradient-to-br from-amber-500/5 to-orange-500/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="relative flex items-center">
                <div class="flex-shrink-0 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl p-2.5 shadow-md group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-xs font-bold text-slate-900 group-hover:text-amber-600 transition-colors">Expense Report</h3>
                    <p class="text-xs text-slate-500 mt-0.5 font-medium">View detailed expense reports</p>
                </div>
            </div>
        </a>
        @endcan
    </div>



    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
        <!-- Pie Chart: Expenses by Expense Head -->
        <div class="bg-gradient-to-br from-white to-slate-50 rounded-2xl shadow-lg border border-slate-200 p-4 hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-200">
                <h3 class="text-base font-bold text-slate-900">Expenses by Expense Head</h3>
                <div class="flex items-center space-x-2 px-2.5 py-1 bg-indigo-50 rounded-lg">
                    <div class="w-2 h-2 bg-indigo-500 rounded-full"></div>
                    <span class="text-xs font-semibold text-indigo-700">Distribution</span>
                </div>
            </div>
            <div class="h-64 flex items-center justify-center">
                <canvas id="expensePieChart" class="w-full h-full"></canvas>
            </div>
        </div>

        <!-- Bar Chart: Monthly Trends -->
        <div class="bg-gradient-to-br from-white to-slate-50 rounded-2xl shadow-lg border border-slate-200 p-4 hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-200">
                <h3 class="text-base font-bold text-slate-900">Monthly Expense Trends</h3>
                <div class="flex items-center space-x-2 px-2.5 py-1 bg-emerald-50 rounded-lg">
                    <div class="w-2 h-2 bg-emerald-500 rounded-full"></div>
                    <span class="text-xs font-semibold text-emerald-700">Last 6 Months</span>
                </div>
            </div>
            <div class="h-64 flex items-center justify-center">
                <canvas id="monthlyTrendChart" class="w-full h-full"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Expenses Table -->
    <div class="bg-gradient-to-br from-white to-slate-50 rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
        <div class="px-4 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900">Recent Expenses</h3>
                @can('view expenses')
                <a href="{{ route('expenses.index') }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-all duration-200">
                    View All
                    <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                @endcan
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Expense Head</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Bank Account</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-700 uppercase tracking-wider">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Details</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-slate-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-100">
                    @forelse($recentExpenses as $entry)
                        <tr class="hover:bg-gradient-to-r hover:from-indigo-50/50 hover:to-purple-50/50 transition-all duration-200 ease-in-out">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-2.5 h-2.5 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-full mr-3 shadow-sm"></div>
                                    <span class="text-xs font-medium text-slate-900">{{ $entry->date_added->format('d M Y') }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gradient-to-r from-indigo-100 to-purple-100 text-indigo-700 border border-indigo-200">
                                    {{ $entry->account->expenseHead->first() ? $entry->account->expenseHead->first()->expense_head : 'N/A' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($entry->voucher && $entry->voucher->bank)
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-slate-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                        </svg>
                                        <span class="text-xs font-medium text-slate-700">{{ $entry->voucher->bank->chartOfAccount->name ?? $entry->voucher->bank->account_name }}</span>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic">N/A</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right">
                                <span class="text-xs font-bold text-red-600">{{ number_format(round($entry->debit_amount), 0) }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="max-w-xs truncate text-xs text-slate-600" title="{{ $entry->voucher->details ?? 'No details' }}">
                                    {{ $entry->voucher->details ?? 'No details' }}
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                <a href="{{ route('expenses.show', $entry->voucher_id) }}" 
                                   class="inline-flex items-center px-2.5 py-1 bg-gradient-to-r from-indigo-500 to-purple-500 text-white rounded-lg hover:from-indigo-600 hover:to-purple-600 transition-all duration-200 text-xs font-semibold shadow-sm hover:shadow-md transform hover:scale-105">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mb-3">
                                        <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <p class="text-xs font-semibold text-slate-600 mb-0.5">No expenses found</p>
                                    <p class="text-xs text-slate-400">Try adjusting your filters or add a new expense</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Filter form functionality
        document.addEventListener('DOMContentLoaded', function() {
            const periodSelect = document.getElementById('filter_type');
            const fromDate = document.getElementById('from_date');
            const toDate = document.getElementById('to_date');

            // Helper function to format date as YYYY-MM-DD in local timezone
            function formatDateLocal(date) {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            function setPeriod(period) {
                const today = new Date();
                let start, end;
                
                if (period === 'current_month') {
                    // First day of current month
                    start = new Date(today.getFullYear(), today.getMonth(), 1);
                    // Last day of current month
                    end = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                    fromDate.setAttribute('disabled', 'disabled');
                    toDate.setAttribute('disabled', 'disabled');
                } else if (period === 'yearly') {
                    // First day of current year
                    start = new Date(today.getFullYear(), 0, 1);
                    // Last day of current year
                    end = new Date(today.getFullYear(), 11, 31);
                    fromDate.setAttribute('disabled', 'disabled');
                    toDate.setAttribute('disabled', 'disabled');
                } else {
                    // Custom - set default to current month but allow editing
                    start = new Date(today.getFullYear(), today.getMonth(), 1);
                    end = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                    fromDate.removeAttribute('disabled');
                    toDate.removeAttribute('disabled');
                }
                
                // Use local date formatting instead of ISO to avoid timezone issues
                fromDate.value = formatDateLocal(start);
                toDate.value = formatDateLocal(end);
            }

            periodSelect.addEventListener('change', function() {
                setPeriod(this.value);
            });

            // On page load, set appropriate dates based on current selection
            setPeriod(periodSelect.value);
        });
        // Pie Chart: Expenses by Expense Head
        const pieCtx = document.getElementById('expensePieChart').getContext('2d');
        const pieChart = new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($expensesByHead->keys()) !!},
                datasets: [{
                    data: {!! json_encode($expensesByHead->values()) !!},
                    backgroundColor: [
                        '#6366F1', '#8B5CF6', '#EC4899', '#EF4444', '#F59E0B',
                        '#10B981', '#06B6D4', '#3B82F6', '#84CC16', '#F97316'
                    ],
                    borderWidth: 0,
                    hoverBorderWidth: 2,
                    hoverBorderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: '#ffffff',
                        borderWidth: 1,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return context.label + ': ' + context.parsed.toLocaleString() + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });

        // Bar Chart: Monthly Trends
        const barCtx = document.getElementById('monthlyTrendChart').getContext('2d');
        const barChart = new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(collect($monthlyTrends)->pluck('month')) !!},
                datasets: [{
                    label: 'Monthly Expenses',
                    data: {!! json_encode(collect($monthlyTrends)->pluck('amount')) !!},
                    backgroundColor: 'rgba(99, 102, 241, 0.8)',
                    borderColor: 'rgba(99, 102, 241, 1)',
                    hoverBackgroundColor: 'rgba(99, 102, 241, 1)',
                    hoverBorderColor: 'rgba(99, 102, 241, 1)',
                    borderWidth: 1,
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
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
                            color: 'rgba(0, 0, 0, 0.1)',
                            drawBorder: false
                        },
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString();
                            },
                            font: {
                                size: 12
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 12
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: '#ffffff',
                        borderWidth: 1,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return 'Amount: ' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    </script>
</x-app-layout> 