<x-app-layout>
    @section('title', 'Detailed General Ledger - Finance Management - StoreBook')

    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'], ['url' => '/finance', 'label' => 'Finance'], ['url' => '#', 'label' => 'Detailed General Ledger']]" />

    <div class="mt-6 space-y-6 max-w-6xl mx-auto">
        <!-- Header + Filters Card -->
        <div class="bg-white shadow-sm rounded-2xl border border-gray-200">
            <div class="px-6 pt-6 pb-4 border-b border-gray-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg bg-indigo-50">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M7 20h10a2 2 0 002-2V6a2 2 0 00-2-2h-3.586a1 1 0 01-.707-.293l-1.414-1.414A1 1 0 0010.586 2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-semibold text-gray-900">Detailed General Ledger</h1>
                        <p class="text-sm text-gray-500">Per-account ledger with running balances for each journal entry.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="window.print()" class="inline-flex items-center px-4 py-2 rounded-lg border border-indigo-200 bg-indigo-50 text-indigo-700 text-sm font-medium hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 print:hidden">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Print Report
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div class="px-6 pb-6 pt-4 bg-gray-50/80 border-b border-gray-100 print:hidden">
                <form method="GET" action="{{ route('finance.detailed-general-ledger.index') }}" class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-5 gap-4 items-end">
                    <div>
                        <label for="from_date" class="block text-xs font-medium text-gray-700 mb-1">From Date</label>
                        <input type="date" id="from_date" name="from_date" value="{{ $fromDate }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label for="to_date" class="block text-xs font-medium text-gray-700 mb-1">To Date</label>
                        <input type="date" id="to_date" name="to_date" value="{{ $toDate }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="md:col-span-2 lg:col-span-2">
                        <label for="account_id" class="block text-xs font-medium text-gray-700 mb-1">Account</label>
                        <select id="account_id" name="account_id" class="chosen-select w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                            <option value="">All Accounts</option>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}" {{ (string)$accountId === (string)$acc->id ? 'selected' : '' }}>
                                    {{ $acc->code }} - {{ $acc->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2 justify-end">
                        <button type="submit" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700 shadow-sm w-full md:w-auto">
                            Apply
                        </button>
                        <a href="{{ route('finance.detailed-general-ledger.index') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-lg border border-gray-300 bg-white text-xs font-semibold text-gray-700 hover:bg-gray-50 w-full md:w-auto">
                            Clear
                        </a>
                    </div>
                </form>

                <div class="mt-4 text-xs text-gray-500">
                    Showing entries from <span class="font-semibold">@businessDate($fromDate)</span> to <span class="font-semibold">@businessDate($toDate)</span>
                    @if($accountId)
                        @php $sel = $accounts->firstWhere('id', $accountId); @endphp
                        @if($sel)
                            &nbsp;for account <span class="font-semibold">{{ $sel->code }} - {{ $sel->name }}</span>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 print:hidden">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <p class="text-xs font-medium text-gray-500 uppercase">Total Accounts</p>
                <p class="mt-2 text-2xl font-bold text-gray-900">{{ count($ledgerData['accounts']) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-red-100 p-4">
                <p class="text-xs font-medium text-red-600 uppercase">Total Debits</p>
                <p class="mt-2 text-2xl font-bold text-red-700">{{ number_format($ledgerData['totals']['debit'], 2) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-green-100 p-4">
                <p class="text-xs font-medium text-green-600 uppercase">Total Credits</p>
                <p class="mt-2 text-2xl font-bold text-green-700">{{ number_format($ledgerData['totals']['credit'], 2) }}</p>
            </div>
        </div>

        <!-- Ledger Content -->
        @forelse($ledgerData['accounts'] as $account)
            <div class="bg-white shadow-sm rounded-2xl border border-gray-200 overflow-hidden break-inside-avoid">
                <!-- Account Header -->
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div>
                        <div class="text-sm font-semibold text-gray-900">
                            {{ $account['code'] }} - {{ $account['name'] }}
                        </div>
                        <div class="text-xs text-gray-500">
                            Type: {{ ucfirst($account['type']) }}
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-4 text-xs">
                        <div>
                            <span class="text-gray-500">Total Debit:</span>
                            <span class="font-semibold text-red-600 ml-1">{{ number_format($account['total_debit'], 2) }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Total Credit:</span>
                            <span class="font-semibold text-green-600 ml-1">{{ number_format($account['total_credit'], 2) }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Ending Balance:</span>
                            <span class="font-semibold ml-1 {{ $account['ending_balance'] >= 0 ? 'text-emerald-700' : 'text-red-700' }}">
                                {{ number_format($account['ending_balance'], 2) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Account Entries Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Date</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Voucher</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Description</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-600">Debit</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-600">Credit</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-600">Running Balance</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach($account['rows'] as $row)
                                <tr class="hover:bg-gray-50 transition-colors {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                                    <td class="px-4 py-2 text-gray-700 whitespace-nowrap">@businessDate($row['date'])</td>
                                    <td class="px-4 py-2 text-gray-700 whitespace-nowrap">
                                        <span class="font-medium">{{ $row['voucher_type'] }}</span>
                                        @if($row['voucher_id'])
                                            <span class="text-gray-500">#{{ $row['voucher_id'] }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-gray-600">
                                        @if($row['comments'])
                                            <span class="line-clamp-2">{{ $row['comments'] }}</span>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-right font-mono text-red-600">
                                        {{ $row['debit'] > 0 ? number_format($row['debit'], 2) : '' }}
                                    </td>
                                    <td class="px-4 py-2 text-right font-mono text-green-600">
                                        {{ $row['credit'] > 0 ? number_format($row['credit'], 2) : '' }}
                                    </td>
                                    <td class="px-4 py-2 text-right font-mono {{ $row['balance'] >= 0 ? 'text-emerald-700' : 'text-red-700' }}">
                                        {{ number_format($row['balance'], 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-8 text-center">
                <p class="text-gray-500 text-sm">No journal entries found for the selected filters.</p>
            </div>
        @endforelse

        <!-- Print styles -->
        <style>
            @media print {
                body {
                    background: white;
                }
                .print\:hidden {
                    display: none !important;
                }
                .mt-6.space-y-6 > div {
                    box-shadow: none !important;
                    border-color: #e5e7eb !important;
                }
                .chosen-container {
                    display: none !important;
                }
                select.chosen-select {
                    display: block !important;
                }
            }
        </style>
    </div>

    <!-- jQuery and Chosen Select -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>
    
    <style>
        /* Chosen Select Styling */
        .chosen-container { 
            width: 100% !important; 
        }
        .chosen-container-single .chosen-single {
            height: 38px;
            line-height: 36px;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            padding: 0 2.25rem 0 0.75rem;
            background: #fff;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            font-size: 0.875rem;
            color: #111827;
        }
        .chosen-container-single .chosen-single span { 
            margin-right: 0.5rem; 
        }
        .chosen-container-single .chosen-single div { 
            right: 0.5rem; 
        }
        .chosen-container-active .chosen-single,
        .chosen-container .chosen-single:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 1px #6366f1 inset, 0 0 0 1px rgba(99,102,241,0.2);
        }
        .chosen-container .chosen-search input {
            height: 36px;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            padding: 0 0.75rem;
        }
        .chosen-container .chosen-results {
            max-height: 240px;
        }
        .chosen-container .chosen-results li.highlighted {
            background-color: #e0e7ff;
            color: #312e81;
        }
    </style>

    <script>
        $(document).ready(function() {
            // Initialize Chosen for account dropdown
            $('#account_id').chosen({
                width: '100%',
                search_contains: true,
                allow_single_deselect: true,
                placeholder_text_single: 'All Accounts'
            });
        });
    </script>
</x-app-layout>


