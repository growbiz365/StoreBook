<x-app-layout>
    @section('title', 'Owner Contributions | ' . config('app.name'))
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('dashboard'), 'label' => 'Dashboard'],
        ['url' => route('settings'), 'label' => 'Settings'],
        ['url' => '#', 'label' => 'Owner Contributions'],
    ]" />

    <div class="mb-3 mt-2 flex flex-col gap-2 border border-gray-200/90 bg-white px-4 py-3 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:gap-3 rounded-lg">
        <div class="min-w-0">
            <h1 class="text-lg font-bold text-gray-900">Owner contributions</h1>
            <p class="text-xs text-gray-500">Capital from owners (cash or bank wallets).</p>
        </div>
        @can('create owner contributions')
        <a href="{{ route('owner-contributions.create') }}"
            class="inline-flex shrink-0 items-center gap-1.5 rounded-md bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            New
        </a>
        @endcan
    </div>

    <x-message />

    <div class="mb-3 grid grid-cols-1 gap-2 sm:grid-cols-3">
        <div class="flex items-center gap-2 rounded-lg border border-gray-200/90 bg-white px-3 py-2 shadow-sm">
            <div class="flex h-8 w-8 items-center justify-center rounded-md bg-emerald-100">
                <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            </div>
            <div>
                <p class="text-[10px] font-medium uppercase tracking-wide text-gray-500">Total</p>
                <p class="text-sm font-bold text-gray-900">{{ $businessCurrencySymbol }}{{ number_format($statistics['total_amount'], 2) }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2 rounded-lg border border-gray-200/90 bg-white px-3 py-2 shadow-sm">
            <div class="flex h-8 w-8 items-center justify-center rounded-md bg-blue-100">
                <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
            </div>
            <div>
                <p class="text-[10px] font-medium uppercase tracking-wide text-gray-500">Records</p>
                <p class="text-sm font-bold text-gray-900">{{ number_format($statistics['total_count']) }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2 rounded-lg border border-gray-200/90 bg-white px-3 py-2 shadow-sm">
            <div class="flex h-8 w-8 items-center justify-center rounded-md bg-violet-100">
                <svg class="h-4 w-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>
            <div>
                <p class="text-[10px] font-medium uppercase tracking-wide text-gray-500">This month</p>
                <p class="text-sm font-bold text-gray-900">{{ $businessCurrencySymbol }}{{ number_format($statistics['this_month'], 2) }}</p>
            </div>
        </div>
    </div>

    <div class="mb-3 rounded-lg border border-gray-200/90 bg-white p-3 shadow-sm">
        <form method="GET" action="{{ route('owner-contributions.index') }}" class="grid grid-cols-1 gap-2 sm:grid-cols-4 sm:gap-3">
            <div>
                <label class="mb-0.5 block text-[10px] font-semibold uppercase text-gray-500">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Reference, description…"
                    class="block w-full rounded-md border border-gray-300 px-2 py-1.5 text-xs focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/30 focus:outline-none" />
            </div>
            <div>
                <label class="mb-0.5 block text-[10px] font-semibold uppercase text-gray-500">From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                    class="block w-full rounded-md border border-gray-300 px-2 py-1.5 text-xs focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/30 focus:outline-none" />
            </div>
            <div>
                <label class="mb-0.5 block text-[10px] font-semibold uppercase text-gray-500">To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                    class="block w-full rounded-md border border-gray-300 px-2 py-1.5 text-xs focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/30 focus:outline-none" />
            </div>
            <div class="flex items-end gap-1.5">
                <button type="submit" class="flex-1 rounded-md bg-gray-800 px-2 py-1.5 text-xs font-semibold text-white hover:bg-gray-700">Filter</button>
                <a href="{{ route('owner-contributions.index') }}" class="rounded-md border border-gray-300 px-2 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50">Reset</a>
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200/90 bg-white shadow-sm">
        @if($contributions->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Via</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Deposit</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Equity (from)</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Received via</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-600">Amount</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach($contributions as $contribution)
                            <tr class="hover:bg-gray-50/80">
                                <td class="whitespace-nowrap px-4 py-3 text-gray-900">{{ $contribution->contribution_date->format('d M Y') }}</td>
                                <td class="min-w-[8rem] px-4 py-3 text-gray-800">
                                    @if($contribution->contribution_via === 'bank' && $contribution->bank)
                                        <span class="font-semibold text-blue-700">Bank</span>
                                        <span class="mt-0.5 block text-xs leading-snug text-gray-600">{{ $contribution->bank->account_name }}</span>
                                    @elseif($contribution->contribution_via === 'cash' && $contribution->bank)
                                        <span class="font-semibold text-green-700">Cash</span>
                                        <span class="mt-0.5 block text-xs leading-snug text-gray-600">{{ $contribution->bank->account_name }}</span>
                                    @else
                                        <span class="font-semibold text-green-700">Cash</span>
                                    @endif
                                </td>
                                <td class="max-w-xs px-4 py-3 text-gray-800">
                                    <span class="font-medium">{{ $contribution->depositAccount->name }}</span>
                                    @if($contribution->depositAccount->code)
                                        <span class="ml-1 text-xs text-gray-500">({{ $contribution->depositAccount->code }})</span>
                                    @endif
                                </td>
                                <td class="max-w-xs px-4 py-3 text-gray-800">
                                    <span class="font-medium">{{ $contribution->fromAccount->name }}</span>
                                    @if($contribution->fromAccount->code)
                                        <span class="ml-1 text-xs text-gray-500">({{ $contribution->fromAccount->code }})</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    <span class="inline-flex rounded-md px-2 py-1 text-xs font-medium
                                        @if($contribution->received_via === 'cash') bg-green-100 text-green-800
                                        @elseif($contribution->received_via === 'bank_transfer') bg-blue-100 text-blue-800
                                        @elseif($contribution->received_via === 'cheque') bg-amber-100 text-amber-800
                                        @else bg-purple-100 text-purple-800
                                        @endif">
                                        {{ ucwords(str_replace('_', ' ', $contribution->received_via)) }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-right text-base font-semibold tabular-nums text-gray-900">
                                    {{ $businessCurrencySymbol }}{{ number_format($contribution->amount, 2) }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    <div class="flex flex-wrap items-center justify-end gap-1.5">
                                        @can('view owner contributions')
                                        <a href="{{ route('owner-contributions.show', $contribution) }}"
                                            class="rounded-md border border-gray-200 bg-white px-2.5 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50">View</a>
                                        @endcan
                                        @can('edit owner contributions')
                                        <a href="{{ route('owner-contributions.edit', $contribution) }}"
                                            class="rounded-md border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-800 hover:bg-emerald-100">Edit</a>
                                        @endcan
                                        @can('delete owner contributions')
                                        <form action="{{ route('owner-contributions.destroy', $contribution) }}" method="POST" class="inline"
                                            onsubmit="return confirm('Delete this contribution? This will also reverse the journal entries and bank ledger.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="rounded-md border border-red-200 bg-red-50 px-2.5 py-1 text-xs font-medium text-red-800 hover:bg-red-100">Delete</button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($contributions->hasPages())
                <div class="border-t border-gray-100 px-4 py-3">
                    {{ $contributions->withQueryString()->links() }}
                </div>
            @endif
        @else
            <div class="flex flex-col items-center justify-center py-10 text-center">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100">
                    <svg class="h-5 w-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <h3 class="mt-2 text-sm font-semibold text-gray-900">No contributions yet</h3>
                <p class="text-xs text-gray-500">Record your first owner contribution.</p>
                @can('create owner contributions')
                <a href="{{ route('owner-contributions.create') }}"
                    class="mt-2 inline-flex items-center gap-1 rounded-md bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">New contribution</a>
                @endcan
            </div>
        @endif
    </div>
</x-app-layout>
