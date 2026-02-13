<x-app-layout>
    @section('title', 'Journal Entries Report - Finance Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        ['url' => '/finance', 'label' => 'Finance'],
        ['url' => '#', 'label' => 'Journal Entries'],
    ]" />

    <div class="bg-white shadow-sm rounded-lg mt-6 border border-gray-200">
        <div class="p-6">
            <!-- Clean Header -->
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">Journal Report</h2>
                        <p class="text-sm text-gray-500">Financial transaction summary</p>
                    </div>
                </div>
                <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium print:hidden transition-colors">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print
                </button>
            </div>

            <!-- Clean Filters -->
            <div class="mb-6 print:hidden">
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Filter Options</h4>
                    <form method="GET" action="{{ route('journal-entries.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="from_date" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                            <input type="date" id="from_date" name="from_date" 
                                   value="{{ request('from_date', $fromDate->format('Y-m-d')) }}"
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="to_date" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                            <input type="date" id="to_date" name="to_date" 
                                   value="{{ request('to_date', $toDate->format('Y-m-d')) }}"
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <input type="text" id="search" name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Search accounts, voucher type..."
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="flex items-end space-x-2">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                Filter
                            </button>
                            <a href="{{ route('journal-entries.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Date Range Display (Moved below filters) -->
            <div class="text-center mb-6 print:mb-4">
                <div class="inline-flex items-center px-4 py-2 bg-blue-50 rounded-lg border border-blue-200">
                    <svg class="w-4 h-4 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span class="text-sm font-medium text-blue-800">
                        From @businessDate($fromDate) To @businessDate($toDate)
                    </span>
                </div>
            </div>

            @if (session('success'))
                <x-success-alert message="{{ session('success') }}" />
            @endif

            <!-- Enhanced Journal Report Table -->
            <div class="overflow-x-auto border border-gray-200 rounded-lg shadow-sm">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 border-r border-gray-200" style="width: 40%;">Account</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 border-r border-gray-200" style="width: 15%;">DEBIT</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 border-r border-gray-200" style="width: 15%;">CREDIT</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700" style="width: 30%;">Comments</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse ($groupedEntries as $groupKey => $entries)
                            @php
                                $firstEntry = $entries->first();
                                $date = $firstEntry->date_added;
                                $voucherType = $firstEntry->voucher_type;
                                $voucherId = $firstEntry->voucher_id;
                                
                                // Calculate transaction totals
                                $transactionDebit = $entries->sum('debit_amount');
                                $transactionCredit = $entries->sum('credit_amount');
                            @endphp
                            
                            <!-- Enhanced Transaction Header -->
                            <tr class="border-b border-gray-200">
                                <td colspan="4" class="px-6 py-4 bg-blue-50 border-b border-blue-200">
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
                                        <span class="text-sm font-semibold text-blue-800">
                                            @businessDate($date) - {{ strtoupper($voucherType) }} {{ $voucherId }}
                                        </span>
                                        <span class="ml-auto text-xs text-blue-600 bg-blue-100 px-2 py-1 rounded-full">
                                            {{ $entries->count() }} entries
                                        </span>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Enhanced Account Entries -->
                            @foreach ($entries as $index => $entry)
                                <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors duration-150 {{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}">
                                    <td class="px-6 py-4 text-sm text-gray-800 border-r border-gray-200">
                                        <div class="flex items-center">
                                            <div class="w-1 h-1 bg-gray-400 rounded-full mr-3"></div>
                                            <span class="font-medium">{{ $entry->account->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-800 text-right border-r border-gray-200 font-mono">
                                        @if($entry->debit_amount > 0)
                                            <span class="text-red-600 font-semibold">{{ number_format(round($entry->debit_amount), 0) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-800 text-right border-r border-gray-200 font-mono">
                                        @if($entry->credit_amount > 0)
                                            <span class="text-green-600 font-semibold">{{ number_format(round($entry->credit_amount), 0) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        @if($entry->comments)
                                            <div class="group relative">
                                                <div class="line-clamp-2">{{ $entry->comments }}</div>
                                                @if(strlen($entry->comments) > 100)
                                                    <div class="hidden group-hover:block absolute z-50 left-0 top-full mt-2 p-4 bg-white rounded-lg shadow-lg border border-gray-200 w-96">
                                                        {{ $entry->comments }}
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            
                            <!-- Enhanced Transaction Total -->
                            <tr class="bg-yellow-50 border-b border-yellow-200">
                                <td class="px-6 py-4 text-sm font-semibold text-gray-800 border-r border-yellow-200">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                        Transaction Total
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-red-700 text-right border-r border-yellow-200 font-mono">
                                    {{ number_format(round($transactionDebit), 0) }}
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-green-700 text-right border-r border-yellow-200 font-mono">
                                    {{ number_format(round($transactionCredit), 0) }}
                                </td>
                                <td class="px-6 py-4"></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="text-gray-500">
                                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Journal Entries Found</h3>
                                        <p class="text-sm">No journal entries found for the selected date range.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        
                        <!-- Enhanced Grand Total -->
                        @if ($groupedEntries->count() > 0)
                            <tr class="bg-gray-100 border-t-2 border-gray-300">
                                <td class="px-6 py-5 text-lg font-bold text-gray-800 border-r border-gray-300">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                        Grand Total
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-lg font-bold text-red-700 text-right border-r border-gray-300 font-mono">
                                    {{ number_format(round($totalDebit), 0) }}
                                </td>
                                <td class="px-6 py-5 text-lg font-bold text-green-700 text-right border-r border-gray-300 font-mono">
                                    {{ number_format(round($totalCredit), 0) }}
                                </td>
                                <td class="px-6 py-5"></td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Enhanced Summary -->
            @if ($groupedEntries->count() > 0)
                <div class="mt-6 print:hidden">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-blue-600 font-medium">Total Transactions</p>
                                    <p class="text-xl font-bold text-blue-800">{{ $groupedEntries->count() }}</p>
                                </div>
                                <div class="p-2 bg-blue-100 rounded-lg">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-red-600 font-medium">Total Debits</p>
                                    <p class="text-xl font-bold text-red-800">{{ number_format(round($totalDebit), 0) }}</p>
                                </div>
                                <div class="p-2 bg-red-100 rounded-lg">
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-green-600 font-medium">Total Credits</p>
                                    <p class="text-xl font-bold text-green-800">{{ number_format(round($totalCredit), 0) }}</p>
                                </div>
                                <div class="p-2 bg-green-100 rounded-lg">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Enhanced Print Styles -->
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .bg-white.shadow-sm.rounded-lg.mt-6, .bg-white.shadow-sm.rounded-lg.mt-6 * {
                visibility: visible;
            }
            .bg-white.shadow-sm.rounded-lg.mt-6 {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                box-shadow: none;
                border: none;
            }
            .print\:hidden {
                display: none !important;
            }
            .print\:mb-4 {
                margin-bottom: 1rem !important;
            }
            table {
                page-break-inside: auto;
                border-collapse: collapse;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            .bg-blue-50, .bg-yellow-50, .bg-gray-100 {
                background: #f9fafb !important;
            }
            .text-red-600, .text-red-700, .text-red-800 {
                color: #dc2626 !important;
            }
            .text-green-600, .text-green-700, .text-green-800 {
                color: #16a34a !important;
            }
            .text-blue-600, .text-blue-700, .text-blue-800 {
                color: #2563eb !important;
            }
        }
    </style>
</x-app-layout>
